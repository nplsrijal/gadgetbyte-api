<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Models\ProductWithCategory;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttribute;
use App\Models\ProductVariantVendor;
use App\Models\ProductVariation;
use App\Models\ProductImage;
use App\Models\ProductPost;
use App\Models\ProductVideo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

use DB;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     summary="Get a list of Products",
     *     tags={"Products"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search term for filtering by name or slug ",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page (optional, default: 20)",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ProductResource"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $perPage=$request->per_page;
            if(empty($perPage)){
                $perPage=20;
            }
            $query = Product::query();
            if ($request->has('q')) {
                $searchTerm = strtoupper($request->input('q'));
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('title', 'ilike', '%' . $searchTerm . '%');


                });
            }


            $data = $query->paginate($perPage)->withPath($request->getPathInfo());
 
         return $this->success(new ProductCollection($data));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

     /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     summary="Create a new Product",
     *     tags={"Products"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="X-User-Id",
     *         in="header",
     *         description="User ID for authentication",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Products data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreProductRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created product",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity (Validation error)",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * )
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        $userId = request()->header('X-User-Id');
        $validated['created_by'] = $userId;
    
        $categories = $validated['categories'] ?? [];
        unset($validated['categories']);
    
        $attributes = $validated['attributes'] ?? [];
        unset($validated['attributes']);
    
        $variations = $validated['variations'] ?? [];
        unset($validated['variations']);
    
        $variants = $validated['variants'] ?? [];
        unset($validated['variants']);

        $images = $validated['images'] ?? [];
        unset($validated['images']);

        $videos = $validated['videos'] ?? [];
        unset($validated['videos']);

        $posts = $validated['posts'] ?? [];
        unset($validated['posts']);
    
        // Begin database transaction
        DB::beginTransaction();
    
        try {
            $data = Product::create($validated);
    
            if (count($categories) > 0) {
                $insert_cat = [];
                foreach ($categories as $cat) {
                    $insert_cat[] = ['product_id' => $data->id, 'category_id' => $cat];
                }
                ProductWithCategory::insert($insert_cat);
            }
            if (count($posts) > 0) {
                $insert_post = [];
                foreach ($posts as $cat) {
                    $insert_post[] = ['product_id' => $data->id, 'post_id' => $cat];
                }
                ProductPost::insert($insert_post);
            }
            if (count($videos) > 0) {
                $insert_video = [];
                foreach ($videos as $cat) {
                    $insert_video[] = ['product_id' => $data->id, 'video_url' => $cat];
                }
                ProductVideo::insert($insert_video);
            }
    
            if (count($attributes) > 0) {
                $insert_attr = [];
                foreach ($attributes as $attr) {
                    $insert_attr[] = [
                        'product_id' => $data->id,
                        'attribute_option_id' => $attr['option_id'],
                        'attribute_name' => $attr['name'],
                        'values' => json_encode($attr['values']) // Ensure values are encoded if they are arrays
                    ];
                }
                ProductAttribute::insert($insert_attr);
            }
    
            if (count($variations) > 0) {
                $insert_variation = [];
                foreach ($variations as $variation) {
                    $insert_variation[] = [
                        'product_id' => $data->id,
                        'variation_name' => $variation['name'],
                      //  'sku_code' => $variation['variation_sku_code'],
                      //  'image_url' => $variation['image_url'],
                        'values' => json_encode($variation['values']) // Ensure values are encoded if they are arrays
                    ];
                }
                ProductVariation::insert($insert_variation);
            }

            if (count($images) > 0) {
                $insert_images = [];
                foreach ($images as $image) {
                    $insert_images[] = [
                        'product_id' => $data->id,
                        'variation_sku_code' => $image['variation_sku_code'],
                        'image_url' =>json_encode($image['image_url']),
                     ];
                }
                ProductImage::insert($insert_images);
            }
    
            if (count($variants) > 0) 
            {
                $insert_variant =$variant_attributes=$variant_vendors= [];
                foreach ($variants as $variant) {
                    $insert_variant[] = [
                        'product_id' => $data->id,
                        'title' => $variant['title'],
                        'slug' => $variant['slug'],
                        'sku_code' => $variant['variation_sku_code'],
                        'price' => $variant['price'],
                        'qty' => $variant['qty'],
                        //'image_url' => $variant['image_url'],
                        'is_default' => $variant['is_default'],
                        'discount_price' => ($variant['discount_price'] > 0) ? $variant['discount_price'] : 0,
                        'discount_price_in' => ($variant['discount_price_in'] != '') ? $variant['discount_price_in'] : null,
                        'discount_price_start_date' => $variant['discount_price_start_date'],
                        'discount_price_end_date' => $variant['discount_price_end_date']
                    ];

                    foreach($variant['attributes'] as $attr)
                    {
                        $variant_attributes[]=array(
                            'product_id'=>$data->id,
                            'variant_slug'=>$variant['slug'],
                            'attribute_name'=>$attr['name'],
                            'values'=>$attr['values'],
                        );
                    }

                    
                    foreach($variant['vendors'] as $vendor)
                    {
                        $variant_vendors[]=array(
                            'product_id'=>$data->id,
                            'vendor_id'=>$vendor['id'],
                            'variant_slug'=>$variant['slug'],
                            'product_url'=>$vendor['product_url'],
                            'discount_price'=>$vendor['discount_price'],
                        );
                    }
                }
                ProductVariant::insert($insert_variant);
                ProductVariantAttribute::insert($variant_attributes);
                ProductVariantVendor::insert($variant_vendors);
            }
    
            DB::commit();
    
            return $this->success(new ProductResource($data), 'Product created', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Could not save Product. ' . $e->getMessage(), 1);
        }
    }
    

    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}",
     *     summary="Get a specific product",
     *     tags={"Products"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the product to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function show(string $id)
    {
        $data = Product::with([
            'categories', 
            'categories.category',
            'attributes',
            'variations',
            'images',
            'videos',
            'variants',
            'variants.variantAttributes', 
            'variants.variantVendors.vendor'     
        ])
        ->join('users','users.id','=','products.created_by')
        ->select('products.*', DB::raw("CONCAT(users.firstname, ' ', users.lastname) as author_name"))
        ->find($id);
        
         if ($data) {
            
            return $this->success(new ProductResource($data));
        } else {
            return $this->error('Product not found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/v1/products/{id}",
     *     summary="Update an existing product",
     *     tags={"Products"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the product to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="X-User-Id",
     *         in="header",
     *         description="User ID for authentication",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Product data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateProductRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated product",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity (Validation error)",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $data = Product::find($id);

        if (!$data) {
            return $this->error('Product not found', Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validated();
        $userId = request()->header('X-User-Id');

        $categories = $validatedData['categories'] ?? [];
        unset($validatedData['categories']);
    
        $attributes = $validatedData['attributes'] ?? [];
        unset($validatedData['attributes']);
    
        $variations = $validatedData['variations'] ?? [];
        unset($validatedData['variations']);
    
        $variants = $validatedData['variants'] ?? [];
        unset($validatedData['variants']);

        $images = $validatedData['images'] ?? [];
        unset($validatedData['images']);

        $videos = $validated['videos'] ?? [];
        unset($validated['videos']);

        $posts = $validated['posts'] ?? [];
        unset($validated['posts']);

        $validatedData['updated_by'] = $userId;
         // Begin database transaction
         DB::beginTransaction();

        $data->update($validatedData);

        if (count($categories) > 0) {
            $postcategory_data=ProductWithCategory::where('product_id', $data->id);
            $postcategory_data->update(['archived_by' => $userId]);
            $postcategory_data->delete();

            $insert_cat = [];
            foreach ($categories as $cat) {
                $insert_cat[] = ['product_id' => $data->id, 'category_id' => $cat];
            }
            ProductWithCategory::insert($insert_cat);
        }

        if (count($posts) > 0) {
            $postcategory_data=ProductPost::where('product_id', $data->id);
            $postcategory_data->update(['archived_by' => $userId]);
            $postcategory_data->delete();
            $insert_post = [];
            foreach ($posts as $cat) {
                $insert_post[] = ['product_id' => $data->id, 'post_id' => $cat];
            }
            ProductPost::insert($insert_post);
        }
        if (count($videos) > 0) {
            $postcategory_data=ProductVideo::where('product_id', $data->id);
            $postcategory_data->update(['archived_by' => $userId]);
            $postcategory_data->delete();
            $insert_video = [];
            foreach ($videos as $cat) {
                $insert_video[] = ['product_id' => $data->id, 'video_url' => $cat];
            }
            ProductVideo::insert($insert_video);
        }

        if (count($attributes) > 0) {
            $delete_data=ProductAttribute::where('product_id', $data->id);
            $delete_data->update(['archived_by' => $userId]);
            $delete_data->delete();
            $insert_attr = [];
            foreach ($attributes as $attr) {
                $insert_attr[] = [
                    'product_id' => $data->id,
                    'attribute_option_id' => $attr['option_id'],
                    'attribute_name' => $attr['name'],
                    'values' => json_encode($attr['values']) // Ensure values are encoded if they are arrays
                ];
            }
            ProductAttribute::insert($insert_attr);
        }

        if (count($variations) > 0) {
            $delete_data=ProductVariation::where('product_id', $data->id);
            $delete_data->update(['archived_by' => $userId]);
            $delete_data->delete();
            $insert_variation = [];
            foreach ($variations as $variation) {
                $insert_variation[] = [
                    'product_id' => $data->id,
                    'variation_name' => $variation['name'],
                  //  'sku_code' => $variation['variation_sku_code'],
                  //  'image_url' => $variation['image_url'],
                    'values' => json_encode($variation['values']) // Ensure values are encoded if they are arrays
                ];
            }
            ProductVariation::insert($insert_variation);
        }

        if (count($images) > 0) {
            $delete_data=ProductImage::where('product_id', $data->id);
            $delete_data->update(['archived_by' => $userId]);
            $delete_data->delete();
            $insert_images = [];
            foreach ($images as $image) {
                $insert_images[] = [
                    'product_id' => $data->id,
                    'variation_sku_code' => $image['variation_sku_code'],
                    'image_url' =>json_encode($image['image_url']),
                 ];
            }
            ProductImage::insert($insert_images);
        }

        if (count($variants) > 0) 
        {
            $delete_data=ProductVariant::where('product_id', $data->id);
            $delete_data->update(['archived_by' => $userId]);
            $delete_data->delete();

            $delete_data=ProductVariantAttribute::where('product_id', $data->id);
            $delete_data->update(['archived_by' => $userId]);
            $delete_data->delete();

            $delete_data=ProductVariantVendor::where('product_id', $data->id);
            $delete_data->update(['archived_by' => $userId]);
            $delete_data->delete();
            $insert_variant =$variant_attributes=$variant_vendors= [];
            foreach ($variants as $variant) {
                $insert_variant[] = [
                    'product_id' => $data->id,
                    'title' => $variant['title'],
                    'slug' => $variant['slug'],
                    'sku_code' => $variant['variation_sku_code'],
                    'price' => $variant['price'],
                    'qty' => $variant['qty'],
                    //'image_url' => $variant['image_url'],
                    'is_default' => $variant['is_default'],
                    'discount_price' => ($variant['discount_price'] > 0) ? $variant['discount_price'] : 0,
                    'discount_price_in' => ($variant['discount_price_in'] != '') ? $variant['discount_price_in'] : null,
                    'discount_price_start_date' => $variant['discount_price_start_date'],
                    'discount_price_end_date' => $variant['discount_price_end_date']
                ];

                foreach($variant['attributes'] as $attr)
                {
                    $variant_attributes[]=array(
                        'product_id'=>$data->id,
                        'variant_slug'=>$variant['slug'],
                        'attribute_name'=>$attr['name'],
                        'values'=>$attr['values'],
                    );
                }

                
                foreach($variant['vendors'] as $vendor)
                {
                    $variant_vendors[]=array(
                        'product_id'=>$data->id,
                        'vendor_id'=>$vendor['id'],
                        'variant_slug'=>$variant['slug'],
                        'product_url'=>$vendor['product_url'],
                        'discount_price'=>$vendor['discount_price'],
                    );
                }
            }
            ProductVariant::insert($insert_variant);
            ProductVariantAttribute::insert($variant_attributes);
            ProductVariantVendor::insert($variant_vendors);
        }
    

         // Check database transaction
         $transactionStatus = DB::transactionLevel();

         if ($transactionStatus > 0) {
             // Database transaction success
             DB::commit();
             return $this->success(new ProductResource($data), 'Product updated', Response::HTTP_OK);
            } else {
             // Throw error
             DB::rollBack();
             throw new Exception('Could not save Post.', 1);
         }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/products/{id}",
     *     summary="Delete an product",
     *     tags={"Products"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the product to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="X-User-Id",
     *         in="header",
     *         description="User ID for authentication",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully deleted product",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Product deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function destroy(string $id)
    {
        $data = Product::find($id);

        if (!$data) {
            return $this->error('Product not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new ProductResource($data), 'Product deleted successfully', Response::HTTP_OK);
    
    }
}
