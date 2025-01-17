<?php

namespace App\Repositories;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/*
    Nos repositórios, simplifiquei o máximo o possível as chamadas da model, neste arquivo tem o método
    mais complexo que envolve a paginação do histórico de adição/deleção de produtos da assinatura.
*/
class SubscriptionPlanRepository
{
    public function getAll(): Collection
    {
        return SubscriptionPlan::all();
    }

    public function getById(int $id): ?SubscriptionPlan
    {
        return SubscriptionPlan::with(['products', 'productsHistory'])->find($id);
    }

    public function getByIdWithHistoryPaginated(int $id, int $page = 1, int $perPage = 10): ?SubscriptionPlan
    {
        //uso as variáveis $page e $perPage vindos da requisição para especificar quantos produtos por página e quantas páginas eu quero que apareça
        return SubscriptionPlan::with(['products', 'productsHistory' => function ($query) use ($page, $perPage) {
            $query->paginate($perPage, ['*'], 'page', $page);
        }])->find($id);
    }

    public function create(array $data): SubscriptionPlan
    {
        return SubscriptionPlan::create($data);
    }

    public function update(int $id, array $data): SubscriptionPlan
    {
        $plan = SubscriptionPlan::find($id);

        if (blank($plan)) { // a função blank() faz todas as verificações possíveis para checar se a variável está vazia ou não.
            throw new NotFoundHttpException("Subscription plan $id not found.", null, Response::HTTP_NOT_FOUND);
        }

        $plan->update($data);

        return $plan;
    }

    public function delete(int $id): bool
    {
        $plan = SubscriptionPlan::find($id);

        if (blank($plan)) {
            throw new NotFoundHttpException("Subscription plan $id not found.", null, Response::HTTP_NOT_FOUND);
        }

        return $plan->delete();
    }
}
