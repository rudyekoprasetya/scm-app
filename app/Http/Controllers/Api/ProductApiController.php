<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('view-products');
        return ProductResource::collection(
            Product::with('category')->where('is_active', true)->latest()->paginate(20)
        );
    }

    public function show(Product $product): ProductResource
    {
        $this->authorize('view-products');
        $product->load('category');
        return new ProductResource($product);
    }

    public function lowStock(): AnonymousResourceCollection
    {
        $this->authorize('view-products');
        return ProductResource::collection(
            Product::with('category')
                ->where('is_active', true)
                ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                ->latest()->paginate(20)
        );
    }
}
