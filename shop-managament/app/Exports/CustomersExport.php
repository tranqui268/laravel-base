<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class CustomersExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $name;
    protected $email;
    protected $isFirstPageOnly;

    public function __construct($name = null, $email = null, $isFirstPageOnly = false)
    {
        $this->name = $name;
        $this->email = $email;
        $this->isFirstPageOnly = $isFirstPageOnly;
    }

    public function query()
    {
        $query = Customer::query();

        if ($this->name) {
            $query->where('customer_name', 'like', '%' . $this->name . '%');
        }

        if ($this->email) {
            $query->where('email', 'like', '%' . $this->email . '%');
        }

        if ($this->isFirstPageOnly) {
            $query->take(10); 
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Tel Num',
            'Address',
            'Is Active',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->customer_name,
            $customer->email,
            $customer->tel_num,
            $customer->address,
            $customer->is_active,
        ];
    }
}
