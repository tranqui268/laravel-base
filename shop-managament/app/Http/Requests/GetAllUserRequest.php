<?php

namespace App\Http\Requests;

use App\Rules\SQLInjectionValidate;
use Illuminate\Foundation\Http\FormRequest;

class GetAllUserRequest extends FormRequest
{
 
    public function authorize(): bool
    {
        return true;
    }
  
    public function rules(): array
    {
            return [
                'name' => [
                    'nullable',
                    'string',
                    'max:255',
                    new SQLInjectionValidate,
                ],
                'email' => [
                    'nullable',
                    'string',
                    'max:255',
                    new SQLInjectionValidate,
                ],
                'group' => [
                    'nullable',
                    'string',
                    'in:user,admin', 
                    new SQLInjectionValidate,
                ],
                'status' => [
                    'nullable',
                    'in:0,1',
                    new SQLInjectionValidate,
                ],
            ];
        
    }

    public function messages()
    {
        return [
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'email.string' => 'Email phải là chuỗi ký tự.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'group.in' => 'Vai trò phải là user hoặc admin.',
            'status.in' => 'Trạng thái phải là 0 hoặc 1.',
        ];
    }
}
