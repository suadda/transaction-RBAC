<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function budget()
    {
        return $this->hasOne(Budget::class);
    }

    /** True jika kategori ini adalah "PO Produk" (case-insensitive) */
    public function isPoProduk(): bool
    {
        return strtolower(trim($this->name)) === 'po produk';
    }

    /**
     * Sisa budget kategori = total budget - total pengajuan yang sudah Paid.
     * Tidak memutasi kolom amount, jadi audit-friendly.
     */
    public function remainingBudget(): float
    {
        $total = (float) ($this->budget->amount ?? 0);

        $used = (float) $this->submissions()
            ->where('status', Submission::PAID)
            ->sum('amount');

        return $total - $used;
    }
}
