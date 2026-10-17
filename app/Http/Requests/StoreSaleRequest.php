<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'qty'         => 'required|integer|min:1',
            'note'        => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak ditemukan.',
            'qty.required'         => 'Jumlah (qty) wajib diisi.',
            'qty.integer'          => 'Jumlah harus berupa angka bulat.',
            'qty.min'              => 'Jumlah minimal 1.',
            'note.max'             => 'Catatan maksimal 500 karakter.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if ($v->errors()->has('category_id') || $v->errors()->has('qty')) {
                return;
            }

            // lockForUpdate agar tidak oversell saat concurrent request
            $cat = Category::lockForUpdate()->find($this->category_id);
            if ($cat && $cat->quota > 0) {
                $terjual = $cat->sales()->sum('qty');
                $qty     = (int) $this->qty;
                if ($terjual + $qty > $cat->quota) {
                    $sisa = max(0, $cat->quota - $terjual);
                    $v->errors()->add('qty', "Kuota {$cat->name} hanya tersisa {$sisa}.");
                }
            }
        });
    }
}
