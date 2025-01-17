<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonRequestHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*
        Este middleware foi criado pois estava recebendo uma tela no insomnia em vez de um JSON, desta maneira o Accept Json fica de forma fixa na header da requisição.
        para que não seja preciso inserir em cada requisição do documento.
        */
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
