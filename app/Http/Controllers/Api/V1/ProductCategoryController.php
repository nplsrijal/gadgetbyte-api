<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\ProductCategoryCollection;
use App\Http\Resources\ProductCategoryResource;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;

class ProductCategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/product-categories",
     *     summary="Get a list of Product Categories",
     *     tags={"Product Categories"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search term for filtering by name or slug ",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="For dropdown or list:(dropdown/list) ",
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ProductCategoryResource"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * )
     */
    public function index(Request $request)
    {
        if($request->type=='list')
        {
            $perPage=$request->per_page;
            if(empty($perPage)){
                $perPage=20;
            }
            $query = ProductCategory::query();
            if ($request->has('q')) {
                $searchTerm = strtoupper($request->input('q'));
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'ilike', '%' . $searchTerm . '%')
                    ->orwhere('slug', 'ilike', '%' . $searchTerm . '%');


                });
            }


            $data = $query->paginate($perPage)->withPath($request->getPathInfo());
        }
        else
        {
            $query = ProductCategory::query();
       
            $query->where('parent_id',0);
            $query->orderBy('order_by');
            $data=$query->get();
            foreach ($data as $key => $menu) {
                $subQuery = ProductCategory::query();
            
                $subQuery->where('parent_id', $menu->id)
                    ->orderBy('order_by');
            
                $submenus = $subQuery->get();
                
                // Assigning submenus to the current menu item
                $data[$key]->children = $submenus;
            }
        }
        return $this->success(new ProductCategoryCollection($data));


        $usertypeId = $request->user_type_id;
       
         $query = ProductCategory::query();
       
         $query->where('parent_id',0);
         $query->orderBy('order_by');
         $data=$query->get();
         foreach ($data as $key => $menu) {
            $subQuery = ProductCategory::query();
        
            $subQuery->where('parent_id', $menu->id)
                ->orderBy('order_by');
        
            $submenus = $subQuery->get();
            
            // Assigning submenus to the current menu item
            $data[$key]->children = $submenus;
        }

 
         return $this->success(new ProductCategoryCollection($data));
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
     *     path="/api/v1/product-categories",
     *     summary="Create a new Product Category",
     *     tags={"Product Categories"},
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
     *         description="Product Categories data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreProductCategoryRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created post category",
     *         @OA\JsonContent(ref="#/components/schemas/ProductCategoryResource")
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
    public function store(StoreProductCategoryRequest $request)
    {
        $validated = $request->validated();
        $userId = request()->header('X-User-Id');
        $validated['created_by'] = $userId;
        $validated['parent_id']=((int)$validated['parent_id'] < 1) ? '0' : $validated['parent_id'];
        
        $data = ProductCategory::create($validated);
       
        return $this->success(new ProductCategoryResource($data), 'ProductCategory created', Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/product-categories/{id}",
     *     summary="Get a specific post category",
     *     tags={"Product Categories"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the post category to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ProductCategoryResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function show(string $id)
    {
        $data = ProductCategory::find($id);

        if ($data) {
            return $this->success(new ProductCategoryResource($data));
        } else {
            return $this->error('ProductCategory not found', Response::HTTP_NOT_FOUND);
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
     *     path="/api/v1/product-categories/{id}",
     *     summary="Update an existing post category",
     *     tags={"Product Categories"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the post category to update",
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
     *         description="ProductCategory data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateProductCategoryRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated post category",
     *         @OA\JsonContent(ref="#/components/schemas/ProductCategoryResource")
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
    public function update(UpdateProductCategoryRequest $request, string $id)
    {
        $data = ProductCategory::find($id);

        if (!$data) {
            return $this->error('ProductCategory not found', Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validated();
        $userId = request()->header('X-User-Id');
        $validatedData['updated_by'] = $userId;
        $data->update($validatedData);
    
        return $this->success(new ProductCategoryResource($data), 'ProductCategory updated', Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/product-categories/{id}",
     *     summary="Delete an post category",
     *     tags={"Product Categories"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the post category to delete",
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
     *         description="Successfully deleted post category",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="ProductCategory deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/ProductCategoryResource")
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
        $data = ProductCategory::find($id);

        if (!$data) {
            return $this->error('ProductCategory not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new ProductCategoryResource($data), 'ProductCategory deleted successfully', Response::HTTP_OK);
    
    }
}
