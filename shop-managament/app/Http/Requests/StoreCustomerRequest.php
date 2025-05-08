<?php

namespace App\Http\Requests;

use App\Rules\SQLInjectionValidate;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
                'string',
                'email',
                'max:255',
                'unique:mst_customer,email',
                new SQLInjectionValidate,
            ],
            'tel_num' => [
                'required',
                'string',              
                'regex:/^[0-9]{10}$/',
                new SQLInjectionValidate,
            ],
            'address' => [
                'required',
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
            'name.required' => 'Tên là bắt buộc.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'name.regex' => 'Tên chỉ được chứa chữ cái, ký tự có dấu và khoảng trắng.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'email.unique' => 'Email đã tồn tại.',
            'tel_num.required' => 'Số điện thoại là bắt buộc.',
            'tel_num.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'tel_num.regex' => 'Số điện thoại chỉ được chứa số.',
            'address.required' => 'Địa chỉ là bắt buộc.',
            'address.string' => 'Địa chỉ phải là chuỗi ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'is_active.in' => 'Trạng thái phải là 0 hoặc 1.',
        ];
    }
}
