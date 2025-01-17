<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//Utilizei o sanctum para a autenticação das rotas do sistema
Route::middleware('auth:sanctum')->group(function () {
    //utilizei do apiResource do laravel para que não seja necessario especificar cada rota das Controllers
    Route::apiResource('users', UserController::class)->only(['index', 'show']); //Já que não há outros métodos além de listagem e detalhes do usuário, chequei somente estes dois
    Route::apiResource('subscription_plans', SubscriptionPlanController::class)->except('update');
    Route::apiResource('products', ProductController::class);

    Route::prefix('/subscription_plans')->group(function () { //utilizei o prefixo para evitar reescrever o mesmo trecho várias vezes
        Route::post('/{id}/add_products', [SubscriptionPlanController::class, 'addProducts'])->name('subscription_plans.add_products');
        Route::delete('/{id}/remove_products', [SubscriptionPlanController::class, 'removeProducts'])->name('subscription_plans.remove_products');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

Route::post('/login', [AuthController::class, 'generateToken'])->name('auth.login');
