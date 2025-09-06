<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreTopupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user doesn't have pending topup request
        $hasPendingTopup = Auth::user()->topupRequests()
            ->pending()
            ->exists();

        return !$hasPendingTopup;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:10000', 'max:10000000'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account' => ['required', 'string', 'max:100'],
            'payment_proof' => ['required', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'amount.min' => 'Minimal topup adalah Rp 10.000',
            'amount.max' => 'Maksimal topup adalah Rp 10.000.000',
            'payment_proof.required' => 'Bukti transfer wajib diupload',
            'payment_proof.mimes' => 'Format file harus jpeg, jpg, png, atau pdf',
            'payment_proof.max' => 'Ukuran file maksimal 2MB',
        ];
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'Anda masih memiliki permintaan topup yang belum diproses. Silakan tunggu hingga disetujui atau ditolak.'
        );
    }
}
