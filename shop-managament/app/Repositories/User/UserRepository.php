<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserRepository extends BaseRepository implements UserRepositoryInterface{
    public function __construct(User $modal){
        parent::__construct($modal);
    }

    public function filters($filters){
        $query = User::query()->where('is_delete', 0);

        if(!empty($filters['name'])){
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }
    
        if (!empty($filters['group'])) {
            $query->where('group_role', $filters['group']);
        }
    
        if (!empty($filters['status'])) {
            $query->where('is_active', $filters['status']);
        }
           
        return $query->orderBy('id','desc')->paginate(10);

    }

    public function create(array $data){
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'group_role' => $data['group_role'] ?? 'user',
            'is_active' => $data['is_active'] ?? 1,
            'is_deleted' => 0,
        ]);

        Log::info('User created', ['user_id' => $user->id, 'email' => $user->email]);

        return $user;

    }

    public function update($id,array $data){
        $user = User::findOrFail($id);
        $updateData = [
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'group_role' => $data['group_role'] ?? $user->group_role,
            'is_active' => isset($data['is_active']) ? $data['is_active'] : $user->is_active,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        Log::info('User updated', ['user_id' => $id, 'data' => $data]);

        return $user;

    }

    public function updateActivedUser($id){
        $user = User::findOrFail($id);
        $user->update([
            'is_active' => ! $user->is_active
        ]);
    }

    public function checkEmail(string $email){
        Log::info('Checking email existence', ['email'=> $email]);
        return User::where('email', $email)
        ->where('is_delete',0)
        ->exists();
    }

    public function checkEmailWithId(string $email, int $id){
        Log::info('Checking email existence', ['email'=> $email]);
        return User::where('email', $email)
        ->where('is_delete',0)
        ->where('id', '!=',$id)
        ->exists();
    }

    public function softDelete($id){
        $user = User::where('is_delete', 0)->findOrFail($id);
        $user->update([
            'is_delete' => 1
        ]);

        Log::info('User deleted', ['user_id' => $id]);

        return true;

    }

    
}