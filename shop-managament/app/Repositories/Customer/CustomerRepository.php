<?php
namespace App\Repositories\Customer;

use App\Repositories\BaseRepository;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface{
    public function __construct(Customer $customer){
        parent::__construct($customer);
    }

    public function filters($filters){
        $query = $this->model->query();

        if(!empty($filters['name'])){
            $query->where('customer_name','like','%'. $filters['name'] .'%');
        }

        if(!empty($filters['email'])){
            $query->where('email','like','%'. $filters['email'] .'%');
        }

        if(!empty($filters['status'])){
            $query->where('is_active', $filters['status']);
        }

        if(!empty($filters['address'])){
            $query->where('address','like','%'. $filters['address'] .'%');
        }
       
        return $query->orderBy('customer_id','desc')->paginate(10);

    }

    public function create(array $data) {
        $customer = Customer::create([
            'customer_name'=> $data['name'],
            'email' => $data['email'],
            'tel_num' => $data['tel_num'],
            'address' => $data['address'],
            'is_active' => $data['is_active'] ?? 1
        ]);
        return $customer;
    }

    public function update($id, array $data) {
        $customers = Customer::findOrFail($id);
        $updateData = [
            'name' => $data['name'] ?? $customers->customers_name,
            'email' => $data['email'] ?? $customers->email,
            'tel_num' => $data['tel_num'],
            'address' => $data['address'],
        ];
       
        $customers->update($updateData);

        Log::info('Customer updated', ['user_id' => $id, 'data' => $data]);

        return $customers;

    }

    public function checkCustomerExist(string $email, int $id){
        Log::info('Checking email existence', ['email'=> $email]);
        return Customer::where('email', $email)
        ->where('customer_id','!=', $id)
        ->exists();
    }

    public function softDelete($id){
        $customer = $this->model->findOrFail($id);
        $customer -> updatate(['is_active' => 0]);
        return true;
    }
    
}