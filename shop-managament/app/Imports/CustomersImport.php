<?php

namespace App\Imports;

use App\Models\Customer;
use App\Rules\SQLInjectionValidate;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CustomersImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $errors = [];
    protected $successCount = 0;
    protected $skippedCount = 0;
    protected $updatedCount = 0;
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $data = [
                'name' => trim($row['name'] ?? ''),
                'email' => trim($row['email'] ?? ''),
                'tel_num' => trim($row['tel_num'] ?? ''),
                'address' => trim($row['address'] ?? ''),
                'is_active' => isset($row['is_active']) ? (int) $row['is_active'] : 1,
            ];

            
            $validator = Validator::make($data, $this->rules(), $this->customValidationMessages());
            if ($validator->fails()) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            try {
                
                $existingCustomer = Customer::where('email', $data['email'])->first();

                if ($existingCustomer) {
                    
                    $isIdentical = $existingCustomer->customer_name === $data['name'] &&
                                   $existingCustomer->tel_num === $data['tel_num'] &&
                                   $existingCustomer->address === $data['address'] &&
                                   $existingCustomer->is_active === $data['is_active'];

                    if ($isIdentical) {
                        
                        $this->skippedCount++;
                        continue;
                    }

                    
                    $existingCustomer->update([
                        'customer_name' => $data['name'],
                        'tel_num' => $data['tel_num'],
                        'address' => $data['address'],
                        'is_active' => $data['is_active'],
                    ]);
                    $this->updatedCount++;
                } else {
                    
                    Customer::create([
                        'customer_name' => $data['name'],
                        'email' => $data['email'],
                        'tel_num' => $data['tel_num'],
                        'address' => $data['address'],
                        'is_active' => $data['is_active'],
                    ]);
                    $this->successCount++;
                }
            } catch (\Exception $e) {
                Log::error('Error importing customer at row ' . ($index + 2) . ': ' . $e->getMessage());
                $this->errors[] = [
                    'row' => $index + 2,
                    'errors' => ['Lỗi hệ thống khi xử lý dòng này.'],
                ];
            }
        }
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
                new SQLInjectionValidate,
            ],
            'tel_num' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9\s\-\+\(\)]+$/',
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

    public function customValidationMessages()
    {
        return [
            'name.required' => 'Tên là bắt buộc.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'name.regex' => 'Tên chỉ được chứa chữ cái, ký tự có dấu và khoảng trắng.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'tel_num.required' => 'Số điện thoại là bắt buộc.',
            'tel_num.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'tel_num.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'tel_num.regex' => 'Số điện thoại chỉ được chứa số, dấu cách, dấu gạch ngang, dấu cộng hoặc dấu ngoặc.',
            'address.required' => 'Địa chỉ là bắt buộc.',
            'address.string' => 'Địa chỉ phải là chuỗi ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'is_active.in' => 'Trạng thái phải là 0 hoặc 1.',
        ];
    }

    public function getImportResult()
    {
        return [
            'success_count' => $this->successCount,
            'updated_count' => $this->updatedCount,
            'skipped_count' => $this->skippedCount,
            'errors' => $this->errors,
        ];
    }

}
