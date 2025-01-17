<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService
{
    public function __construct(
        protected UserRepository $repository
    )
    {}

    public function getAllUsers(): Collection
    {
        return $this->repository->all();
    }

    public function getUserById(int $id): User
    {
        $user = $this->repository->find($id);

        if (blank($user)) {
            throw new NotFoundHttpException("User $id not found.");
        }

        return $user;
    }
}
