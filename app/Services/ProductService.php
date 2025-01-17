<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/*
    Nas services temos uma estrutura semelhante com a da controller, com uma injeção de dependência mas dessa vez do repositório
    e os métodos carregando toda a regra de negócio de aplicação, gosto desse Design Pattern pela facilidade de reutilizar estes
    métodos em outros locais do projeto caso necessáro.
*/
class ProductService
{
    public function __construct(
        protected ProductRepository $repository
    )
    {}

    public function getAll(): Collection
    {
        $products = $this->repository->getAll();

        Log::info('Products search result', [
            'products'   => print_r($products->toArray(), true),
            'user_id'    => Auth::user()->id
        ]);

        return $products;
    }

    public function getById(int $id): Product
    {
        $product = $this->repository->getById($id);

        if (blank($product)) {
            throw new NotFoundHttpException("Product $id not found.", null, Response::HTTP_NOT_FOUND);
        }

        Log::info("Product $id search result", [
            'product'   => print_r($product->toArray(), true),
            'user_id'   => Auth::user()->id
        ]);

        return $product;
    }

    public function create(array $data): Product
    {
        $product = $this->repository->create($data);

        Log::info("Product created", [
            'product'    => print_r($product->toArray(), true),
            'user_id'    => Auth::user()->id
        ]);

        return $product;
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->repository->update($id, $data);

        Log::info("Product $id updated", [
            'product'    => print_r($product->toArray(), true),
            'user_id' => Auth::user()->id
        ]);

        return $product;
    }

    public function delete(int $id): void
    {
        $delete = $this->repository->delete($id);

        if (!$delete) {
            throw new Exception("Product $id not deleted.", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        Log::info("Product $id deleted", [
            'product_id' => $id,
            'user_id'    => Auth::user()->id
        ]);
    }
}
