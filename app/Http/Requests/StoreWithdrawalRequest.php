<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWithdrawalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account_holder_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-Z\s]+$/'
            ],
            'account_number' => [
                'required',
                'string',
                'min:6',
                'max:20',
                'regex:/^[0-9\-]+$/'
            ],
            'bank_name' => [
                'required',
                'string',
                'min:3',
                'max:100'
            ],
            'bank_code' => [
                'nullable',
                'string',
                'max:10'
            ],
            'amount' => [
                'required',
                'numeric',
                'min:10000', // Minimum withdrawal 10k
                'max:10000000', // Maximum withdrawal 10M
                function ($attribute, $value, $fail) {
                    if (auth()->user() && !auth()->user()->hasSufficientBalance($value)) {
                        $fail('Saldo tidak mencukupi untuk penarikan sebesar ' . number_format($value, 0, ',', '.'));
                    }
                }
            ],
            'notes' => [
                'nullable',
                'string',
                'max:500'
            ]
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'account_holder_name.required' => 'Nama pemegang rekening harus diisi.',
            'account_holder_name.regex' => 'Nama pemegang rekening hanya boleh berisi huruf dan spasi.',
            'account_holder_name.min' => 'Nama pemegang rekening minimal 2 karakter.',
            'account_holder_name.max' => 'Nama pemegang rekening maksimal 100 karakter.',
            
            'account_number.required' => 'Nomor rekening harus diisi.',
            'account_number.regex' => 'Nomor rekening hanya boleh berisi angka dan tanda hubung.',
            'account_number.min' => 'Nomor rekening minimal 6 karakter.',
            'account_number.max' => 'Nomor rekening maksimal 20 karakter.',
            
            'bank_name.required' => 'Nama bank harus diisi.',
            'bank_name.min' => 'Nama bank minimal 3 karakter.',
            'bank_name.max' => 'Nama bank maksimal 100 karakter.',
            
            'amount.required' => 'Nominal penarikan harus diisi.',
            'amount.numeric' => 'Nominal penarikan harus berupa angka.',
            'amount.min' => 'Nominal penarikan minimal Rp 10.000.',
            'amount.max' => 'Nominal penarikan maksimal Rp 10.000.000.',
            
            'notes.max' => 'Catatan maksimal 500 karakter.',
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
            'account_holder_name' => 'nama pemegang rekening',
            'account_number' => 'nomor rekening',
            'bank_name' => 'nama bank',
            'bank_code' => 'kode bank',
            'amount' => 'nominal penarikan',
            'notes' => 'catatan',
        ];
    }

    /**
     * Calculate admin fee based on amount
     *
     * @return float
     */
    public function getAdminFee(): float
    {
        $amount = (float) $this->input('amount', 0);
        
        // Admin fee calculation
        if ($amount <= 100000) {
            return 2500; // Flat fee for amount <= 100k
        } elseif ($amount <= 500000) {
            return $amount * 0.025; // 2.5% for 100k-500k
        } elseif ($amount <= 1000000) {
            return $amount * 0.02; // 2% for 500k-1M
        } else {
            return $amount * 0.015; // 1.5% for > 1M
        }
    }

    /**
     * Get total amount that will be deducted from balance
     *
     * @return float
     */
    public function getTotalDeducted(): float
    {
        return (float) $this->input('amount', 0) + $this->getAdminFee();
    }
}
