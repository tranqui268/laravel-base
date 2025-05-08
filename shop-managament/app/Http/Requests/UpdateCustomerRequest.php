<?php

namespace App\Http\Requests;

use App\Rules\SQLInjectionValidate;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidId;

class UpdateCustomerRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['sometimes', new ValidId],
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
            'tel_num' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^[0-9\s\-\+\(\)]+$/',
                new SQLInjectionValidate,
            ],
            'address' => [
                'sometimes',
                'string',
                'max:255',
                new SQLInjectionValidate,
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
            'tel_num.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'tel_num.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'tel_num.regex' => 'Số điện thoại chỉ được chứa số, dấu cách, dấu gạch ngang, dấu cộng hoặc dấu ngoặc.',
            'address.string' => 'Địa chỉ phải là chuỗi ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'is_active.in' => 'Trạng thái phải là 0 hoặc 1.',
        ];
    }
}
