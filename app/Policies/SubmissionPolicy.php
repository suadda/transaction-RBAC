<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;
use App\Services\WorkflowService;

class SubmissionPolicy
{
    public function __construct(private WorkflowService $workflow) {}

    /**
     * Staff hanya boleh melihat pengajuan miliknya sendiri.
     */
    public function view(User $user, Submission $submission): bool
    {
        return $submission->user_id === $user->id;
    }

    /**
     * Approver (SPV/Manager/Direktur) hanya boleh approve/reject
     * jika role-nya adalah tahap yang sedang ditunggu submission ini.
     */
    public function act(User $user, Submission $submission): bool
    {
        return optional($user->role)->name === $this->workflow->currentRole($submission);
    }
}
