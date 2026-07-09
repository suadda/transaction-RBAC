<?php

namespace Tests\Feature;

use App\Models\Approval;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Role;
use App\Models\Submission;
use App\Models\User;
use App\Services\WorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    use RefreshDatabase;

    private WorkflowService $workflow;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workflow = app(WorkflowService::class);

        foreach (['Staff', 'SPV', 'Manager', 'Direktur', 'Finance'] as $r) {
            Role::create(['name' => $r]);
        }

        $this->makeCategory('PO Produk', 100_000_000);
        $this->makeCategory('Operasional', 50_000_000);
        $this->makeCategory('Kecil', 5_000_000);
    }

    private function makeCategory(string $name, float $budget): Category
    {
        $c = Category::create(['name' => $name]);
        Budget::create(['category_id' => $c->id, 'amount' => $budget]);
        return $c;
    }

    private function user(string $roleName): User
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        return User::create([
            'name'     => $roleName . ' User',
            'email'    => strtolower($roleName) . uniqid() . '@test.com',
            'password' => 'password',
            'role_id'  => $role->id,
        ]);
    }

    /** Buat pengajuan lalu jalankan workflow start(); kembalikan model fresh */
    private function submit(string $category, float $amount): Submission
    {
        $cat = Category::where('name', $category)->firstOrFail();

        $submission = Submission::create([
            'submission_no' => 'REQ-TEST-' . uniqid(),
            'date'          => now(),
            'user_id'       => $this->user('Staff')->id,
            'category_id'   => $cat->id,
            'amount'        => $amount,
            'description'   => 'pengujian',
            'status'        => Submission::SUBMITTED,
        ]);

        $this->workflow->start($submission);
        return $submission->fresh();
    }

    /** Kondisi 1: Kategori PO Produk langsung ke Direktur (berapa pun nominalnya) */
    public function test_po_produk_langsung_ke_direktur(): void
    {
        $s = $this->submit('PO Produk', 2_000_000);
        $this->assertEquals(Submission::WAITING_DIRECTOR, $s->status);
    }

    /** Kondisi 2a: Non-PO, nominal <= 5jt cukup SPV, lalu ke Finance */
    public function test_nominal_kecil_cukup_spv(): void
    {
        $s = $this->submit('Operasional', 3_000_000);
        $this->assertEquals(Submission::WAITING_SPV, $s->status);

        $this->workflow->approve($s, $this->user('SPV'));
        $this->assertEquals(Submission::WAITING_FINANCE, $s->fresh()->status);
    }

    /** Kondisi 2b: Non-PO, 5jt < nominal <= 10jt => SPV lalu Manager */
    public function test_nominal_menengah_spv_lalu_manager(): void
    {
        $s = $this->submit('Operasional', 8_000_000);
        $this->assertEquals(Submission::WAITING_SPV, $s->status);

        $this->workflow->approve($s, $this->user('SPV'));
        $this->assertEquals(Submission::WAITING_MANAGER, $s->fresh()->status);

        $this->workflow->approve($s->fresh(), $this->user('Manager'));
        $this->assertEquals(Submission::WAITING_FINANCE, $s->fresh()->status);
    }

    /** Kondisi 3: nominal > 10jt => SPV, Manager, lalu Direktur */
    public function test_nominal_besar_spv_manager_direktur(): void
    {
        // pakai PO? tidak. Operasional budget 50jt cukup untuk 15jt.
        $s = $this->submit('Operasional', 15_000_000);
        $this->assertEquals(Submission::WAITING_SPV, $s->status);

        $this->workflow->approve($s, $this->user('SPV'));
        $this->assertEquals(Submission::WAITING_MANAGER, $s->fresh()->status);

        $this->workflow->approve($s->fresh(), $this->user('Manager'));
        $this->assertEquals(Submission::WAITING_DIRECTOR, $s->fresh()->status);

        $this->workflow->approve($s->fresh(), $this->user('Direktur'));
        $this->assertEquals(Submission::WAITING_FINANCE, $s->fresh()->status);
    }

    /** Kondisi 4: budget kategori tidak cukup => langsung Rejected */
    public function test_budget_tidak_cukup_langsung_ditolak(): void
    {
        $s = $this->submit('Operasional', 60_000_000); // budget hanya 50jt
        $this->assertEquals(Submission::REJECTED, $s->status);
    }

    /** Kondisi 5: salah satu approver reject => Rejected, alur berhenti */
    public function test_reject_menghentikan_alur(): void
    {
        $s = $this->submit('Operasional', 8_000_000);

        $this->workflow->reject($s, $this->user('SPV'), 'tidak sesuai anggaran');

        $this->assertEquals(Submission::REJECTED, $s->fresh()->status);
        $this->assertDatabaseHas('approvals', [
            'submission_id' => $s->id,
            'status'        => Approval::REJECTED,
        ]);
    }

    /** Kondisi 6 & 7: seluruh approval selesai => Finance bayar => Paid */
    public function test_finance_memproses_pembayaran(): void
    {
        $s = $this->submit('Operasional', 3_000_000);
        $this->workflow->approve($s, $this->user('SPV'));
        $s->refresh();
        $this->assertEquals(Submission::WAITING_FINANCE, $s->status);

        $ok = $this->workflow->pay($s, $this->user('Finance'));

        $this->assertTrue($ok);
        $this->assertEquals(Submission::PAID, $s->fresh()->status);
        $this->assertDatabaseHas('payments', ['submission_id' => $s->id]);
    }

    /** Kondisi 7 (negatif): saldo/budget habis di tahap Finance => Rejected */
    public function test_finance_menolak_bila_budget_habis(): void
    {
        // Kategori "Kecil" budget 5jt. Dua pengajuan @3jt (<=5jt => cukup SPV).
        // Saat submit, belum ada yang Paid, jadi keduanya lolos cek budget awal.
        $a = $this->submit('Kecil', 3_000_000);
        $b = $this->submit('Kecil', 3_000_000);

        $this->workflow->approve($a, $this->user('SPV'));
        $this->workflow->approve($b, $this->user('SPV'));
        $this->assertEquals(Submission::WAITING_FINANCE, $a->fresh()->status);
        $this->assertEquals(Submission::WAITING_FINANCE, $b->fresh()->status);

        // Bayar A => sisa budget 5jt - 3jt = 2jt
        $this->assertTrue($this->workflow->pay($a->fresh(), $this->user('Finance')));
        $this->assertEquals(Submission::PAID, $a->fresh()->status);

        // Bayar B => 2jt < 3jt => ditolak di tahap Finance
        $this->assertFalse($this->workflow->pay($b->fresh(), $this->user('Finance')));
        $this->assertEquals(Submission::REJECTED, $b->fresh()->status);
    }

    /** Staff tidak boleh membuka halaman approval */
    public function test_staff_dilarang_akses_approval(): void
    {
        $this->actingAs($this->user('Staff'))
            ->get('/approval')
            ->assertForbidden();
    }

    /** SPV boleh membuka halaman approval */
    public function test_spv_boleh_akses_approval(): void
    {
        $this->actingAs($this->user('SPV'))
            ->get('/approval')
            ->assertOk();
    }

    /** Staff tidak boleh membuka halaman finance */
    public function test_staff_dilarang_akses_finance(): void
    {
        $this->actingAs($this->user('Staff'))
            ->get('/finance')
            ->assertForbidden();
    }
}
