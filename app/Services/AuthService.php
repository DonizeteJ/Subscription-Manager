<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AuthService
{
    public function __construct(
		private UserRepository $userRepository
	) {}

	public function generateToken(array $userData): string
	{
		$user = $this->userRepository->findByEmail($userData['email']);

        if (blank($user)) {
            throw new NotFoundHttpException('User not found.', null, Response::HTTP_NOT_FOUND);
        }

        $checkPass = Hash::check($userData['password'], $user->password);

        if (!$checkPass) {
            throw new UnprocessableEntityHttpException('Invalid password.', null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

		$user->tokens()->delete();

		$token = $user->createToken($userData['email'])->plainTextToken;

		Log::info('Token generated', [
			'email' => $userData['email']
		]);

		return $token;
	}

	public function logout(): void
	{
		$user = $this->userRepository->find(Auth::user()->id);

		Log::info('User ' . Auth::user()->id . ' logged out');

		$user->tokens()->delete();
	}
}
