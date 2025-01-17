<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Exceptions\UnprocessableEntityException;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/*
    Neste arquivo explicarei algumas decisões que tomei que servem para todas as controllers.
    Estou utilizando o padrão de projeto de Serviços e Repositórios, assim, a controller é
    reponsável somente por receber as chamadas de API e redirecionar pra lógica correspondente.
*/
class AuthController extends Controller
{
    public function __construct(
        private AuthService $service //Injeção de dependência para que não seja preciso criar uma nova instância do serviço de autenticação toda vez que chamarmos um dos seus métodos.
    ) {}

    public function generateToken(LoginRequest $request): JsonResponse //Utilizei Requests customizadas para limpar a controller das regras do payload
    {
        try {
            $token = $this->service->generateToken($request->validated());

            return response()->json([
                'token' => $token
            ], Response::HTTP_CREATED);
        } catch (UnprocessableEntityHttpException|NotFoundHttpException $exception) {  //tratei as exceptions geradas na service aqui, nesse caso, são respostas específicas para caso não encotrarmos um usuário valido por meio do e-mail ou caso a hash não corresponda a senha inserida.
            return response()->json([
                'message' => $exception->getMessage()
            ], $exception->getCode());
        } catch (Exception $exception) { //neste outro catch tratamos exceptions mais comuns
            Log::error('Error generating token', [
                'error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'email' => $request->email
            ]);

            return response()->json([
                'message' => 'An error occurred while generating the token.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->service->logout();

            return response()->json([
                'message' => 'Successfully disconnected.'
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            Log::error('Error generating token', [
                'error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'message' => 'An error occurred while loging out.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
