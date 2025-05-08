<?php

namespace App\Http\Requests;

use App\Rules\SQLInjectionValidate;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\s]+$/u',
                new SQLInjectionValidate,
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:mst_users,email',
                'regex:/^[a-zA-Z0-9@._-]+$/',
                new SQLInjectionValidate,
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/^[a-zA-Z0-9]+$/',
                new SQLInjectionValidate,
            ],
            'group_role' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\s]+$/',
                new SQLInjectionValidate,
            ],
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên là bắt buộc.',
            'name.regex' => 'Tên không được chứa ký tự đặc biệt.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'email.regex' => 'Email chỉ được chứa chữ, số, @, ., -, _.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.regex' => 'Mật khẩu chỉ được chứa chữ và số.',
            'group_role.regex' => 'Vai trò không được chứa ký tự đặc biệt.',
        ];
    }
}
