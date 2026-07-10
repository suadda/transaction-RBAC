<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function __construct(private WorkflowService $workflow) {}

    /** Daftar pengajuan yang menunggu persetujuan role user yang login */
    public function index()
    {
        $role   = Auth::user()->role->name;
        $status = [
            'SPV'      => Submission::WAITING_SPV,
            'Manager'  => Submission::WAITING_MANAGER,
            'Direktur' => Submission::WAITING_DIRECTOR,
        ][$role] ?? null;

        $submissions = Submission::with('category', 'user')
            ->where('status', $status)
            ->latest()
            ->get();

        return view('approval.index', compact('submissions', 'role'));
    }

    public function show(Submission $submission)
    {
        $submission->load('category', 'user', 'approvals.user');
        return view('approval.show', compact('submission'));
    }

    public function approve(Request $request, Submission $submission)
    {
        $this->authorize('act', $submission);
        $data = $request->validate(['comment' => 'nullable|string']);

        $this->workflow->approve($submission, Auth::user(), $data['comment'] ?? null);

        return redirect()->route('approval.index')
            ->with('success', "Pengajuan {$submission->submission_no} disetujui.");
    }

    public function reject(Request $request, Submission $submission)
    {
        $this->authorize('act', $submission);
        $data = $request->validate(['comment' => 'required|string']);

        $this->workflow->reject($submission, Auth::user(), $data['comment']);

        return redirect()->route('approval.index')
            ->with('success', "Pengajuan {$submission->submission_no} ditolak.");
    }
}
