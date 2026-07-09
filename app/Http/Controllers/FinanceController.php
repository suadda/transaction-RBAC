<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    public function __construct(private WorkflowService $workflow) {}

    public function index()
    {
        $submissions = Submission::with('category', 'user')
            ->where('status', Submission::WAITING_FINANCE)
            ->latest()
            ->get();

        return view('finance.index', compact('submissions'));
    }

    public function pay(Request $request, Submission $submission)
    {
        abort_unless($submission->status === Submission::WAITING_FINANCE, 403);

        $data = $request->validate(['note' => 'nullable|string']);
        $ok   = $this->workflow->pay($submission, Auth::user(), $data['note'] ?? null);

        $msg = $ok
            ? "Pembayaran {$submission->submission_no} berhasil. Status: Paid."
            : "Saldo/budget tidak mencukupi. Pengajuan {$submission->submission_no} ditolak.";

        return redirect()->route('finance.index')->with('success', $msg);
    }
}
