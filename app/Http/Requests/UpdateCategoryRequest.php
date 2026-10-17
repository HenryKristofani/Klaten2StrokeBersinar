<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:100',
            'price' => 'required|integer|min:0',
            'quota' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Nama kategori wajib diisi.',
            'name.max'       => 'Nama kategori maksimal 100 karakter.',
            'price.required' => 'Harga wajib diisi.',
            'price.integer'  => 'Harga harus berupa angka bulat.',
            'price.min'      => 'Harga tidak boleh negatif.',
            'quota.integer'  => 'Kuota harus berupa angka bulat.',
            'quota.min'      => 'Kuota tidak boleh negatif.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'quota' => $this->quota ?? 0,
        ]);
    }

    /** Validasi bisnis: kuota baru tidak boleh lebih kecil dari yang sudah terjual */
    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $category = $this->route('category');
            $quota    = (int) $this->quota;

            if ($quota > 0 && $category) {
                $terjual = $category->sales()->sum('qty');
                if ($quota < $terjual) {
                    $v->errors()->add(
                        'quota',
                        "Kuota tidak boleh lebih kecil dari jumlah yang sudah terjual ({$terjual})."
                    );
                }
            }
        });
    }
}
