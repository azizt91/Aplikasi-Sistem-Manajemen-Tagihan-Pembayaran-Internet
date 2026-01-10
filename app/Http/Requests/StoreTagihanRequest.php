<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk validasi pembuatan tagihan.
 * 
 * Memisahkan validasi dari controller untuk:
 * - Kode yang lebih bersih
 * - Reusable validation rules
 * - Custom error messages
 */
class StoreTagihanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization ditangani oleh middleware auth
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000|max:2100',
            'id_pelanggan' => 'required|array|min:1',
            'id_pelanggan.*' => 'exists:pelanggan,id_pelanggan',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bulan.required' => 'Bulan harus dipilih',
            'bulan.integer' => 'Bulan harus berupa angka',
            'bulan.min' => 'Bulan tidak valid (minimal 1)',
            'bulan.max' => 'Bulan tidak valid (maksimal 12)',
            'tahun.required' => 'Tahun harus dipilih',
            'tahun.integer' => 'Tahun harus berupa angka',
            'tahun.min' => 'Tahun minimal 2000',
            'tahun.max' => 'Tahun maksimal 2100',
            'id_pelanggan.required' => 'Minimal satu pelanggan harus dipilih',
            'id_pelanggan.array' => 'Data pelanggan tidak valid',
            'id_pelanggan.min' => 'Minimal satu pelanggan harus dipilih',
            'id_pelanggan.*.exists' => 'Pelanggan yang dipilih tidak ditemukan',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'bulan' => 'bulan tagihan',
            'tahun' => 'tahun tagihan',
            'id_pelanggan' => 'pelanggan',
        ];
    }
}
