<?php

namespace App\Http\Requests;

use App\Rules\SQLInjectionValidate;
use App\Rules\ValidId;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

   
    public function rules(): array
    {
        return [
            'id' => ['sometimes', new ValidId],
            'name' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[\p{L}\s]+$/u',
                new SQLInjectionValidate,
            ],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                new SQLInjectionValidate,
            ],
            'password' => [
                'nullable',
                'string',
                'min:6',
                new SQLInjectionValidate,
            ],
            'group_role' => [
                'sometimes',
                'in:user,admin',
            ],
            'is_active' => [
                'sometimes',
                'in:0,1',
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'name.regex' => 'Tên chỉ được chứa chữ cái, ký tự có dấu và khoảng trắng.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'group_role.in' => 'Vai trò phải là user hoặc admin.',
            'is_active.in' => 'Trạng thái phải là 0 hoặc 1.',
        ];
    }
}
