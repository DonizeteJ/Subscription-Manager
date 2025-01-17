<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    public function __construct(
        protected UserService $service
    )
    {}

    /**
     * Display a listing of the users.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $users = $this->service->getAllUsers();

            Log::info('Users search result', [
                'users'   => print_r($users->toArray(), true),
                'user_id' => Auth::user()->id
            ]);

            return response()->json($users, Response::HTTP_OK);
        } catch (Exception $exception) {
            Log::error('Error in user search', [
                'error'   => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'file'    => $exception->getFile(),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'error' => 'An error occurred while fetching users. Please contact an administrator.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->service->getUserById($id);

            Log::info("User $id search result", [
                'user'   => print_r($user->toArray(), true),
                'user_id' => Auth::user()->id
            ]);

            return response()->json($user, Response::HTTP_OK);
        } catch (NotFoundHttpException $exception) {
            Log::error("User $id not found", [
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'error' => $exception->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            Log::error('Error searching for user' . $id, [
                'error'   => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'file'    => $exception->getFile(),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'error' => "An error occurred while searching for user $id. Please contact an administrator."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
