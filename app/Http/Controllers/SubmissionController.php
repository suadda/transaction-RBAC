<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Submission;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function __construct(private WorkflowService $workflow) {}

    /** Riwayat pengajuan milik staff yang login */
    public function index()
    {
        $submissions = Submission::with('category')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('staff.submissions.index', compact('submissions'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('staff.dashboard', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'            => 'required|date',
            'category_id'     => 'required|exists:categories,id',
            'amount'          => 'required|numeric|min:1',
            'description'     => 'required|string',
            'attachment_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('attachment_path')) {
            $filePath = $request->file('attachment_path')->store('attachments', 'public');
        }

        $submission = Submission::create([
            'submission_no'   => 'REQ-' . date('Ymd') . '-' . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT),
            'date'            => $validated['date'],
            'user_id'         => Auth::id(),
            'category_id'     => $validated['category_id'],
            'amount'          => $validated['amount'],
            'description'     => $validated['description'],
            'attachment_path' => $filePath,
            'status'          => Submission::SUBMITTED,
        ]);

        // Jalankan mesin workflow: routing ke approver pertama / tolak bila budget kurang
        $this->workflow->start($submission);

        return redirect()
            ->route('staff.submissions.index')
            ->with('success', 'Pengajuan berhasil dikirim. Status: ' . $submission->fresh()->status);
    }

    /** Detail + timeline approval */
    public function show(Submission $submission)
    {
        abort_unless($submission->user_id === Auth::id(), 403);

        $submission->load('category', 'approvals.user', 'payment');
        return view('staff.submissions.show', compact('submission'));
    }
}
