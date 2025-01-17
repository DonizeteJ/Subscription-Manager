<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\SubscriptionPlan;
use App\Repositories\AuditRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SubscriptionPlanRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubscriptionPlanService
{
    public function __construct(
        protected SubscriptionPlanRepository $subscriptionPlanRepository,
        protected ProductRepository $productRepository,
        protected AuditRepository $auditRepository
    )
    {}

    public function getAll(): Collection
    {
        $plans = $this->subscriptionPlanRepository->getAll();

        Log::info('Subscription plans search result', [
            'plans'   => print_r($plans->toArray(), true),
            'user_id' => Auth::user()->id
        ]);

        return $plans;
    }

    public function getById(int $id, int $page, int $perPage): SubscriptionPlan
    {
        $plan = $this->subscriptionPlanRepository->getByIdWithHistoryPaginated($id, $page, $perPage);

        if (blank($plan)) {
            throw new NotFoundHttpException("Subscription plan $id not found.", null, Response::HTTP_NOT_FOUND);
        }

        Log::info("Subscription plan $id search result", [
            'plan'    => print_r($plan->toArray(), true),
            'products_history' => print_r($plan->productsHistory->toArray(), true),
            'user_id' => Auth::user()->id
        ]);

        return $plan;
    }

    public function create(array $data): SubscriptionPlan
    {
        $productIds = $data['product_ids'];

        unset($data['product_ids']);

        DB::beginTransaction(); //Utilizei as transações de banco para evitar casos onde o plano de assinatura é criado mesmo com os produtos inseridos sendo inválidos, neste caso fazemos um rollback.

        $plan = $this->subscriptionPlanRepository->create($data);

        $this->verifyIfProductsExists($productIds, true);

        $plan->products()->sync($productIds);

        $this->auditAddedProducts($plan, $productIds);

        Log::info("Subscription plan created", [
            'plan'    => print_r($plan->toArray(), true),
            'products' => print_r($plan->products->toArray(), true),
            'user_id' => Auth::user()->id
        ]);

        DB::commit();

        return $plan;
    }

    public function delete(int $id): void
    {
        $delete = $this->subscriptionPlanRepository->delete($id);

        if (!$delete) {
            throw new Exception("Subscription plan $id not deleted.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        Log::info("Subscription plan $id deleted", [
            'plan_id' => $id,
            'user_id' => Auth::user()->id
        ]);
    }

    public function addProducts(int $id, array $data): void
    {
        $productIds = $data['product_ids'];

        $plan = $this->subscriptionPlanRepository->getById($id);

        if (blank($plan)) {
            throw new NotFoundHttpException("Subscription plan $id not found.", null, Response::HTTP_NOT_FOUND);
        }

        $this->verifyIfProductsExists($productIds);

        foreach ($productIds as $productId) {
            $plan->products()->attach($productId); //usei o método attach e sync do eloquent pois eles facilitam a inserção de dados em models com relacionamento muitos pra muitos.
        }

        $this->auditAddedProducts($plan, $productIds);
    }

    public function removeProducts(int $id, array $data): void
    {
        $productIds = $data['product_ids'];

        $plan = $this->subscriptionPlanRepository->getById($id);

        if (blank($plan)) {
            throw new NotFoundHttpException("Subscription plan $id not found.", null, Response::HTTP_NOT_FOUND);
        }

        $this->verifyIfProductsExists($productIds);

        foreach ($productIds as $productId) {
            $plan->products()->detach($productId);
        }

        $this->auditRemovedProducts($plan, $productIds);
    }

    private function verifyIfProductsExists(array $productIds, bool $onCreation = false): void
    {
        $existingProducts = $this->productRepository->getProductIds($productIds);

        $nonExistentProductIds = array_diff($productIds, $existingProducts);

        $message = "Products with IDs ". implode(", ", $nonExistentProductIds) . " not found.";

        if (filled($nonExistentProductIds)) {
            if ($onCreation) {
                $message = "Can't create subscription plan. " . $message;

                DB::rollBack();
            }

            Log::error($message, [
                'product_ids' => $productIds,
                'non_existent_product_ids' => $nonExistentProductIds,
                'user_id' => Auth::user()->id
            ]);

            throw new NotFoundHttpException($message, null, Response::HTTP_NOT_FOUND);
        }
    }

    private function auditAddedProducts(SubscriptionPlan $plan, array $productIds, bool $attach = false): void
    {
        foreach ($productIds as $productId) {
            $this->createAudit($plan, $productId, Audit::ACTION_ADDED);
        }

        Log::info("Products added to subscription plan $plan->id", [
            'product_ids' => print_r($productIds, true),
            'user_id' => Auth::user()->id
        ]);
    }

    private function auditRemovedProducts(SubscriptionPlan $plan, array $productIds): void
    {
        foreach ($productIds as $productId) {
            $this->createAudit($plan, $productId, Audit::ACTION_DELETED);
        }

        Log::info("Products removed from subscription plan $plan->id", [
            'product_ids' => print_r($productIds, true),
            'user_id' => Auth::user()->id
        ]);
    }

    private function createAudit(SubscriptionPlan $plan, int $productId, string $action): void
    {
        $this->auditRepository->create([
            'user_id' => Auth::user()->id,
            'subscription_plan_id' => $plan->id,
            'product_id' => $productId,
            'action' => $action,
            'created_at' => Carbon::now()
        ]);
    }
}
