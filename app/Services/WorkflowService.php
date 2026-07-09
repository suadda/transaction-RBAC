<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\User;
use App\Models\Approval;
use App\Models\Payment;

/**
 * Mesin workflow approval. Semua aturan routing & status ada di sini,
 * supaya controller tetap tipis dan aturannya mudah diuji.
 *
 * Rantai approver (sesuai Rules soal):
 *  - Kategori "PO Produk"           => [Direktur]
 *  - Bukan PO, nilai <= 5.000.000   => [SPV]
 *  - Bukan PO, 5jt < nilai <= 10jt  => [SPV, Manager]
 *  - Bukan PO, nilai > 10.000.000   => [SPV, Manager, Direktur]
 */
class WorkflowService
{
    /** Peta role approver -> status "menunggu" pada submission */
    private const WAITING_STATUS = [
        'SPV'      => Submission::WAITING_SPV,
        'Manager'  => Submission::WAITING_MANAGER,
        'Direktur' => Submission::WAITING_DIRECTOR,
    ];

    /**
     * Tentukan urutan role approver untuk sebuah submission.
     */
    public function chainFor(Submission $submission): array
    {
        $category = $submission->category;
        $amount   = (float) $submission->amount;

        if ($category && $category->isPoProduk()) {
            return ['Direktur'];
        }

        if ($amount > 10_000_000) {
            return ['SPV', 'Manager', 'Direktur'];
        }

        if ($amount > 5_000_000) {
            return ['SPV', 'Manager'];
        }

        return ['SPV'];
    }

    /**
     * Dipanggil setelah Staff submit. Cek budget lalu arahkan ke approver pertama.
     */
    public function start(Submission $submission): void
    {
        // Kondisi 4: budget kategori tidak mencukupi => Ditolak
        if (! $this->budgetIsSufficient($submission)) {
            $submission->update(['status' => Submission::REJECTED]);
            return;
        }

        $firstRole = $this->chainFor($submission)[0];
        $submission->update(['status' => self::WAITING_STATUS[$firstRole]]);
    }

    /**
     * Role yang sedang ditunggu persetujuannya, berdasarkan status saat ini.
     */
    public function currentRole(Submission $submission): ?string
    {
        return array_search($submission->status, self::WAITING_STATUS, true) ?: null;
    }

    /**
     * Approver menyetujui. Mencatat ke tabel approvals lalu memajukan status.
     */
    public function approve(Submission $submission, User $approver, ?string $comment = null): void
    {
        $role = $this->currentRole($submission);

        // Catat jejak approval
        Approval::create([
            'submission_id' => $submission->id,
            'user_id'       => $approver->id,
            'role'          => $role,
            'status'        => Approval::APPROVED,
            'comment'       => $comment,
        ]);

        // Cek ulang budget setiap kali maju (Kondisi 4)
        if (! $this->budgetIsSufficient($submission)) {
            $submission->update(['status' => Submission::REJECTED]);
            return;
        }

        // Cari role berikutnya dalam rantai
        $chain = $this->chainFor($submission);
        $index = array_search($role, $chain, true);
        $next  = $chain[$index + 1] ?? null;

        if ($next) {
            $submission->update(['status' => self::WAITING_STATUS[$next]]);
        } else {
            // Kondisi 6: seluruh approval selesai => Menunggu Finance
            $submission->update(['status' => Submission::WAITING_FINANCE]);
        }
    }

    /**
     * Approver menolak. Kondisi 5: langsung Rejected, alur berhenti.
     */
    public function reject(Submission $submission, User $approver, ?string $comment = null): void
    {
        Approval::create([
            'submission_id' => $submission->id,
            'user_id'       => $approver->id,
            'role'          => $this->currentRole($submission),
            'status'        => Approval::REJECTED,
            'comment'       => $comment,
        ]);

        $submission->update(['status' => Submission::REJECTED]);
    }

    /**
     * Finance memproses pembayaran. Kondisi 7: cek saldo/budget.
     */
    public function pay(Submission $submission, User $finance, ?string $note = null): bool
    {
        // Saldo diinterpretasikan sebagai sisa budget kategori (lihat README)
        if (! $this->budgetIsSufficient($submission)) {
            $submission->update(['status' => Submission::REJECTED]);
            return false;
        }

        Payment::create([
            'submission_id' => $submission->id,
            'amount'        => $submission->amount,
            'paid_by'       => $finance->id,
            'paid_at'       => now(),
            'note'          => $note,
        ]);

        $submission->update(['status' => Submission::PAID]);
        return true;
    }

    /**
     * Budget kategori masih cukup untuk menampung nilai pengajuan ini?
     */
    private function budgetIsSufficient(Submission $submission): bool
    {
        $category = $submission->category;
        if (! $category) {
            return false;
        }

        return $category->remainingBudget() >= (float) $submission->amount;
    }
}
