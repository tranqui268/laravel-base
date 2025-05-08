<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetAllUserRequest;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Rules\ValidId;

use App\Services\UserService;

class UsersController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function getAllUser(GetAllUserRequest $request){
        $users = $this->userRepository->filters($request);
        return response()->json([
            'data' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'page_size' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ]
        ]);
    }

    public function createUser(StoreUserRequest $request)
    {
        $user = $this->userRepository->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }
    
    public function updateUser($id,UpdateUserRequest $request)
    {
        $user = $this->userRepository->update($id,$request->validated());
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully', 
            'user' => $user
        ]);
    }

    public function updateActiveUser($id){
        $this->userRepository->updateActivedUser($id);
        return response()->json([
            'success' => true,
            'message'=> 'Update successfully'
        ]);
    }

    public function softDeleteUser($id)
    {
        $this->validatedId($id);
        $this->userRepository->softDelete($id);
        return response()->json([
            'success' => true,
            'message'=> 'Update successfully'
        ]);
    }

    public function checkEmail(Request $request){
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);
        $exists = $this->userRepository->checkEmail($request->input('email'));
        return response()->json(['exists' => $exists]);
    }

    public function checkEmailWithId(Request $request){
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'id' => ['required', new ValidId],
        ]);
        $exists = $this->userRepository->checkEmailWithId($request->input('email'), $request->input('id'));
        return response()->json(['exists' => $exists]);
    }

    protected function validatedId($id){
        validator(['id'=>$id],
        [
            'id' => [new ValidId]
        ])->validate();
    }
}
