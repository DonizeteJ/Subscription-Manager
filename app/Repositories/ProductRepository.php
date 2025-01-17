<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductRepository
{
    public function getAll(): Collection
    {
        return Product::all();
    }

    public function getById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function getProductIds(array $ids): array
    {
        return Product::whereIn('id', $ids)->pluck('id')->toArray();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = Product::find($id);

        if (blank($product)) {
            throw new NotFoundHttpException("Product $id not found.", null, Response::HTTP_NOT_FOUND);
        }

        $product->update($data);

        return $product;
    }

    public function delete(int $id): bool
    {
        $product = Product::find($id);

        if (blank($product)) {
            throw new NotFoundHttpException("Product $id not found.", null, Response::HTTP_NOT_FOUND);
        }

        return $product->delete();
    }
}
