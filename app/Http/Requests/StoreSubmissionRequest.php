<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    /**
     * Hanya Staff yang boleh membuat pengajuan.
     * Lapisan kedua setelah middleware role:Staff pada route.
     */
    public function authorize(): bool
    {
        return optional($this->user()->role)->name === 'Staff';
    }

    /**
     * Aturan validasi (dipindah dari controller).
     */
    public function rules(): array
    {
        return [
            'date'            => ['required', 'date'],
            'category_id'     => ['required', 'exists:categories,id'],
            'amount'          => ['required', 'numeric', 'min:1'],
            'description'     => ['required', 'string'],
            'attachment_path' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    /**
     * Pesan error dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'category_id.required'  => 'Kategori wajib dipilih.',
            'amount.min'            => 'Nominal harus lebih dari 0.',
            'attachment_path.mimes' => 'Lampiran harus berupa PDF, JPG, JPEG, atau PNG.',
            'attachment_path.max'   => 'Ukuran lampiran maksimal 5 MB.',
        ];
    }
}
