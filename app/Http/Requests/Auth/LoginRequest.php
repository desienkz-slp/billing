<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:51              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Requests\Auth; use Illuminate\Foundation\Http\FormRequest; class LoginRequest extends FormRequest { public function authorize(): bool { return true; } public function rules(): array { return ['username' => ['required', 'string', 'max:100'], 'password' => ['required', 'string', 'min:4'], 'device_name' => ['nullable', 'string', 'max:255']]; } public function messages(): array { return ['username.required' => 'Username wajib diisi.', 'password.required' => 'Password wajib diisi.', 'password.min' => 'Password minimal 4 karakter.']; } }
