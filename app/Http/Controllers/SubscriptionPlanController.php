<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionPlan\AddProductRequest;
use App\Http\Requests\SubscriptionPlan\RemoveProductRequest;
use App\Http\Requests\SubscriptionPlan\StoreSubscriptionPlanRequest;
use App\Services\SubscriptionPlanService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubscriptionPlanController extends Controller
{
    public function __construct(
        protected SubscriptionPlanService $service
    )
    {}

    public function index(): JsonResponse
    {
        try {
            $plans = $this->service->getAll();

            return response()->json($plans, Response::HTTP_OK);
        } catch (Exception $exception) {
            Log::error('Error while fetching subscription plans.', [
                'error'   => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'file'    => $exception->getFile(),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'message' => 'An error occurred while fetching subscription plans.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreSubscriptionPlanRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::user()->id;

            $plan = $this->service->create($data);

            return response()->json($plan, Response::HTTP_CREATED);
        } catch (NotFoundHttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        } catch (Exception $exception) {
            Log::error('Error while creating subscription plan.', [
                'error'   => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'file'    => $exception->getFile(),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'message' => 'An error occurred while creating subscription plan.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $page = (int) $request->input('history_page', 1);
            $perPage = (int) $request->input('per_page', 10);

            $planData = $this->service->getById($id, $page, $perPage);

            return response()->json($planData, Response::HTTP_OK);
        } catch (NotFoundHttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        } catch (Exception $exception) {
            Log::error('Error while fetching subscription plan ' . $id, [
                'error'   => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'file'    => $exception->getFile(),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'message' => 'An error occurred while fetching subscription plan ' . $id,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return response()->json([
                'message' => 'Subscription plan deleted successfully.'
            ], Response::HTTP_OK);
        } catch (NotFoundHttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        } catch (Exception $exception) {
            Log::error('Error while deleting subscription plan ' . $id, [
                'error'   => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'file'    => $exception->getFile(),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'message' => 'An error occurred while deleting subscription plan ' . $id,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addProducts(int $id, AddProductRequest $request)
    {
        try {
            $this->service->addProducts($id, $request->validated());

            return response()->json([
                'message' => 'Products added to subscription plan successfully.'
            ], Response::HTTP_OK);
        } catch (NotFoundHttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        } catch (Exception $exception) {
            Log::error('Error while adding products to subscription plan ' . $id, [
                'error'   => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'file'    => $exception->getFile(),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'message' => 'An error occurred while adding products to subscription plan ' . $id,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function removeProducts(int $id, RemoveProductRequest $request)
    {
        try {
            $this->service->removeProducts($id, $request->validated());

            return response()->json([
                'message' => 'Products removed from subscription plan successfully.'
            ], Response::HTTP_OK);
        } catch (NotFoundHttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        } catch (Exception $exception) {
            Log::error('Error while removing products from subscription plan ' . $id, [
                'error'   => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'file'    => $exception->getFile(),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'message' => 'An error occurred while removing products from subscription plan ' . $id,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
