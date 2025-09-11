<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');
        $userId = $user ? $user->id : null;

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:6'],
            'balance' => ['nullable', 'integer', 'min:0'],
            'level' => ['nullable', 'integer', 'min:1', 'max:10'],
            'credit_score' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'visitors' => ['nullable', 'integer', 'min:0'],
            'followers' => ['nullable', 'integer', 'min:0'],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }
}
