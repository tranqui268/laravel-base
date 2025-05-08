<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Repositories\Customer\CustomerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Rules\ValidId;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport;
use App\Imports\CustomersImport;

class CustomerController extends Controller
{
  
    protected $customerRepository;
    public function __construct(CustomerRepositoryInterface $customerRepository){      
        $this->customerRepository = $customerRepository;
    }
    public function getAllCustomer(Request $request){
        $customers = $this->customerRepository->filters($request);
        return response()->json([
            'data' => $customers->items(),
            'pagination' => [
                'total' => $customers->total(),
                'page_size' => $customers->perPage(),
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
            ]
        ]);
    }

    public function createCustomer(StoreCustomerRequest $request){
        try {
            $customer = $this->customerRepository->create($request->validated());
            return response()->json([
                "success"=> true,
                'message'=>'Customer create successfull',
                'customer' => $customer
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error in createCustomer: ' . $e->getMessage());
            return response()->json([
                'success'=> false,
                'error' => 'Lỗi khi tạo khách hàng'
            ], 500);
        }
    }

    public function updateCustomer($id,UpdateCustomerRequest $request){
        try {
            $customer = $this->customerRepository->update($id,$request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully', 
                'customer' => $customer
            ]);
        } catch (\Exception $e) {
            Log::error('Error in update Customer: ' . $e->getMessage());
            return response()->json([
                'success'=> false,
                'error' => 'Lỗi khi cập nhật khách hàng',
                'message' => $e->getMessage()
            ], 500);
        }

    }

    public function checkEmailWithId(Request $request){
        try {
            $request->validate([
                'email' => ['required', 'string', 'email', 'max:255'],
                'customer_id' => ['required', new ValidId],
            ]);
            $exists = $this->customerRepository->checkCustomerExist($request->input('email'), $request->input('customer_id'));  
            return response()->json(['exists' => $exists]);
        } catch (\Exception $e) {
            Log::error('Error in check customer exist: ' . $e->getMessage());
            return response()->json([
                'success'=> false,
                'error' => 'Lỗi khi kiểm tra email khách hàng'
            ], 500);
        }
    }

    public function importCustomers(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,xlsx|max:2048',
            ]);

            $import = new CustomersImport();
            Excel::import($import, $request->file('file'));

            $result = $import->getImportResult();

            return response()->json([
                'message' => 'Import completed',
                'success_count' => $result['success_count'],
                'updated_count' => $result['updated_count'],
                'skipped_count' => $result['skipped_count'],
                'errors' => $result['errors'],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in importCustomers: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi import file'], 500);
        }
    }

    public function exportCustomers(Request $request)
    {
        try {
            $name = $request->query('name');
            $email = $request->query('email');
            $isFirstPageOnly = !$name && !$email;

            $export = new CustomersExport($name, $email, $isFirstPageOnly);
            return Excel::download($export, 'customers_' . now()->format('Ymd_His') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Error in exportCustomers: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi khi export file'], 500);
        }
    }

    protected function validatedId($id){
        validator(['id'=>$id],
        [
            'id' => [new ValidId]
        ])->validate();
    }
}
