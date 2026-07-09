<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Submission; 
use Illuminate\Support\Facades\Auth; 

class SubmissionController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        return view('staff.dashboard', compact('categories'));
    }

    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:1', 
            'description' => 'required|string',
            'attachment_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('attachment_path')) {
            $filePath = $request->file('attachment_path')->store('attachments', 'public');
        }

        $submissionNo = 'REQ-' . date('Ymd') . '-' . rand(1000, 9999);

        Submission::create([
            'submission_no' => $submissionNo,
            'date' => $request->date,
            'user_id' => Auth::id(), 
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'attachment_path' => $filePath,
            'status' => 'Submitted',
        ]);

        return redirect()->back()->with('success', 'Mantap! Pengajuan transaksi berhasil dikirim.');
    }
}