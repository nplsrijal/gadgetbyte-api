<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\BrandCollection;
use App\Http\Resources\BrandResource;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;

class BrandController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/brands",
     *     summary="Get a list of Brands",
     *     tags={"Brands"},
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/BrandResource"))
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
            $query = Brand::query();
            if ($request->has('q')) {
                $searchTerm = strtoupper($request->input('q'));
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'ilike', '%' . $searchTerm . '%')
                    ->orwhere('slug', 'ilike', '%' . $searchTerm . '%');


                });
            }


            $data = $query->paginate($perPage)->withPath($request->getPathInfo());
 
         return $this->success(new BrandCollection($data));
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
     *     path="/api/v1/brands",
     *     summary="Create a new Brand",
     *     tags={"Brands"},
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
     *         description="Brands data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreBrandRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created brand",
     *         @OA\JsonContent(ref="#/components/schemas/BrandResource")
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
    public function store(StoreBrandRequest $request)
    {
        $validated = $request->validated();
        $userId = request()->header('X-User-Id');
        $validated['created_by'] = $userId;
        
        $data = Brand::create($validated);
       
        return $this->success(new BrandResource($data), 'Brand created', Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/brands/{id}",
     *     summary="Get a specific brand",
     *     tags={"Brands"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the brand to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/BrandResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function show(string $id)
    {
        $data = Brand::find($id);

        if ($data) {
            return $this->success(new BrandResource($data));
        } else {
            return $this->error('Brand not found', Response::HTTP_NOT_FOUND);
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
     *     path="/api/v1/brands/{id}",
     *     summary="Update an existing brand",
     *     tags={"Brands"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the brand to update",
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
     *         description="Brand data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateBrandRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated brand",
     *         @OA\JsonContent(ref="#/components/schemas/BrandResource")
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
    public function update(UpdateBrandRequest $request, string $id)
    {
        $data = Brand::find($id);

        if (!$data) {
            return $this->error('Brand not found', Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validated();
        $userId = request()->header('X-User-Id');
        $validatedData['updated_by'] = $userId;
        $data->update($validatedData);
    
        return $this->success(new BrandResource($data), 'Brand updated', Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/brands/{id}",
     *     summary="Delete an brand",
     *     tags={"Brands"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the brand to delete",
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
     *         description="Successfully deleted brand",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Brand deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/BrandResource")
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
        $data = Brand::find($id);

        if (!$data) {
            return $this->error('Brand not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new BrandResource($data), 'Brand deleted successfully', Response::HTTP_OK);
    
    }
}
