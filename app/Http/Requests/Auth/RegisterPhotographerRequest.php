<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterPhotographerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role !== 'superadmin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $authenticated = $this->user() !== null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'studio_name' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:30', 'regex:/^(?:\+?62|0)[0-9\s().-]{7,24}$/'],
            'email' => [Rule::requiredIf(! $authenticated), Rule::excludeIf($authenticated), 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => [Rule::requiredIf(! $authenticated), Rule::excludeIf($authenticated), 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email ini sudah terdaftar. Silakan login untuk mengaktifkan akses Photographer pada akun Anda.',
            'whatsapp.regex' => 'Nomor WhatsApp harus berupa nomor Indonesia yang valid.',
        ];
    }
}
