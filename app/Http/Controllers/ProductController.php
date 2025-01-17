<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Services\ProductService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    )
    {}

    public function index(): JsonResponse
    {
        try {
            $products = $this->service->getAll();

            return response()->json($products, Response::HTTP_OK);
        } catch (Exception $exception) {
            Log::error('Error while fetching products.', [
                'error'   => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'file'    => $exception->getFile(),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'message' => 'An error occurred while fetching products.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->service->create($request->validated());

            return response()->json($product, Response::HTTP_CREATED);
        } catch (Exception $exception) {
            Log::error('Error while creating product.', [
                'error'   => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'file'    => $exception->getFile(),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'message' => 'An error occurred while creating product.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->service->getById($id);

            return response()->json($product, Response::HTTP_OK);
        } catch (NotFoundHttpException $exception) { // Mais uma das exceptions específicas descritas no arquivo AuthController, caso o produto não seja encontrado, NotFoundException é gerada e tratada.
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        } catch (Exception $exception) {
            Log::error('Error while fetching product ' . $id, [
                'error'   => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'file'    => $exception->getFile(),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'message' => 'An error occurred while fetching product ' . $id,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->service->update($id, $request->validated());

            return response()->json($product, Response::HTTP_OK);
        } catch (NotFoundHttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        } catch (Exception $exception) {
            Log::error('Error while updating product ' . $id, [
                'error'   => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'file'    => $exception->getFile(),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'message' => 'An error occurred while updating product ' . $id,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return response()->json([
                'message' => 'Product deleted successfully.'
            ], Response::HTTP_OK);
        } catch (NotFoundHttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        } catch (Exception $exception) {
            Log::error('Error while deleting product ' . $id, [
                'error'   => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'file'    => $exception->getFile(),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'message' => 'An error occurred while deleting product ' . $id,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
