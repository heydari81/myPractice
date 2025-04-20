<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Jobs\NotifyAdminOfNewProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ProductController extends ApiController
{
    public function index()
    {
        $products = Cache::remember('products', 3600, function () {
            return Product::all();
        });
//        dd(Cache::get('products'));
//        $products = Product::paginate(10);
        return $this->successResponse(201, [
            'products' => ProductResource::collection($products),
//            'links' => ProductResource::collection($products)->response()->getData()->links
        ], 'all Products');
    }

    public function store(CreateProductRequest $request, Product $product)
    {
        $product = $product->newProduct($request);
        NotifyAdminOfNewProduct::dispatch($product->id);
        $responseData = $product->orderBy('id', 'desc')->first();
        return $this->successResponse(201, new ProductResource($responseData), 'created product');
    }

    public function show(Product $product)
    {
        return $this->successResponse(201, new ProductResource($product), 'show product');
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $slugUnique = Product::query()->where('slug', $request->slug)->where('id', '!=', $product->id)->exists();

        if ($slugUnique) {
            return $this->errorResponse(409, 'The slug already exists');
        }
        $product->updateProduct($request);
        return $this->successResponse(201, new ProductResource($product), 'update product');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return $this->successResponse(200, [], 'deleted product');
    }
    public function search(Request $request)
    {
        $query = $request->query('q', '');
        if (empty($query)) {
            return $this->errorResponse(400, 'Search query is required');
        }

        $products =  Product::where('name', 'LIKE', '%' . $query . '%')->get();

        return $this->successResponse(200, $products, 'Search results');
    }
}
