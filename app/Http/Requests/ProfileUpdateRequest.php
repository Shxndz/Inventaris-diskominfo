<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'      => ['sometimes', 'required', 'string', 'max:255'],
            'nip'       => ['sometimes', 'required', 'digits:18', Rule::unique(User::class)->ignore($this->user()->id)],
            'email'     => ['sometimes', 'required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'no_hp'     => ['sometimes', 'required', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'jabatan'   => ['sometimes', 'required', 'string', 'max:100'],
            'sekbid_id' => ['sometimes', 'required', 'exists:sekbids,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nip.digits'  => 'NIP harus terdiri dari 18 digit angka.',
            'no_hp.regex' => 'Format nomor HP tidak valid (contoh: 081234567890).',
        ];
    }
}
