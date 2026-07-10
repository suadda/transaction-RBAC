<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Jumlah pengajuan per status
        $byStatus = Submission::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Ringkasan kartu atas
        $stats = [
            'total'     => Submission::count(),
            'pending'   => Submission::whereIn('status', [
                Submission::WAITING_SPV,
                Submission::WAITING_MANAGER,
                Submission::WAITING_DIRECTOR,
                Submission::WAITING_FINANCE,
            ])->count(),
            'paid'      => Submission::where('status', Submission::PAID)->count(),
            'rejected'  => Submission::where('status', Submission::REJECTED)->count(),
            'paid_amount' => (float) Submission::where('status', Submission::PAID)->sum('amount'),
        ];

        // Sisa budget per kategori
        $categories = Category::with('budget')->get()->map(function ($c) {
            $total = (float) ($c->budget->amount ?? 0);
            $remaining = $c->remainingBudget();
            return [
                'name'      => $c->name,
                'total'     => $total,
                'used'      => $total - $remaining,
                'remaining' => $remaining,
                'percent'   => $total > 0 ? round((($total - $remaining) / $total) * 100) : 0,
            ];
        });

        return view('dashboard', compact('byStatus', 'stats', 'categories'));
    }
}
