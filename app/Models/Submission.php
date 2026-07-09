<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    /** Daftar status agar tidak menulis string manual di mana-mana */
    public const DRAFT            = 'Draft';
    public const SUBMITTED        = 'Submitted';
    public const WAITING_SPV      = 'Waiting SPV Approval';
    public const WAITING_MANAGER  = 'Waiting Manager Approval';
    public const WAITING_DIRECTOR = 'Waiting Director Approval';
    public const WAITING_FINANCE  = 'Waiting Finance';
    public const PAID             = 'Paid';
    public const REJECTED         = 'Rejected';

    protected $fillable = [
        'submission_no',
        'date',
        'user_id',
        'category_id',
        'amount',
        'description',
        'attachment_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date'   => 'date',
            'amount' => 'decimal:2',
        ];
    }

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
