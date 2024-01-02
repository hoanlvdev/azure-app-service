<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        return $this->userRepository->getAll();
    }

    public function getUserById($id)
    {
        return $this->userRepository->getById($id);
    }

    public function createUser($data)
    {
        return $this->userRepository->save($data);
    }

    public function updateUser($id, $data)
    {
        return $this->userRepository->update($id, $data);
    }

    public function deleteUser($id)
    {
        $this->userRepository->delete($id);
    }

    public function findUserPagination(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $filter = $request->input('filter', null);
        $sort = $request->input('sort', null);
        $columns = $request->input('columns', ['*']);

        return $this->userRepository->paginate($page, $perPage, $filter, $sort, $columns);
    }
}
