<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\CategoryShowcase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\CategoryShowCaseCollection;
use App\Http\Resources\CategoryShowCaseResource;
use App\Http\Requests\StoreCategoryShowCaseRequest;
use App\Http\Requests\UpdateCategoryShowCaseRequest;

class CategoryShowcaseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/category-showcases",
     *     summary="Get a list of CategoryShowcases",
     *     tags={"CategoryShowcases"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search term for filtering by name ",
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CategoryShowCaseResource"))
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
            $query = CategoryShowcase::query();
            if ($request->has('q')) {
                $searchTerm = strtoupper($request->input('q'));
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'ilike', '%' . $searchTerm . '%');


                });
            }


            $data = $query->paginate($perPage)->withPath($request->getPathInfo());
 
         return $this->success(new CategoryShowCaseCollection($data));
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
     *     path="/api/v1/category-showcases",
     *     summary="Create a new CategoryShowcase",
     *     tags={"CategoryShowcases"},
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
     *         description="CategoryShowcases data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreCategoryShowCaseRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created vendor",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryShowCaseResource")
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
    public function store(StoreCategoryShowCaseRequest $request)
    {
        $validated = $request->validated();
        $userId = request()->header('X-User-Id');
        $validated['created_by'] = $userId;
        
        $data = CategoryShowcase::create($validated);
       
        return $this->success(new CategoryShowCaseResource($data), 'CategoryShowcase created', Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/category-showcases/{id}",
     *     summary="Get a specific vendor",
     *     tags={"CategoryShowcases"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the vendor to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryShowCaseResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function show(string $id)
    {
        $data = CategoryShowcase::find($id);

        if ($data) {
            return $this->success(new CategoryShowCaseResource($data));
        } else {
            return $this->error('CategoryShowcase not found', Response::HTTP_NOT_FOUND);
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
     *     path="/api/v1/category-showcases/{id}",
     *     summary="Update an existing vendor",
     *     tags={"CategoryShowcases"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the vendor to update",
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
     *         description="CategoryShowcase data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateCategoryShowCaseRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated vendor",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryShowCaseResource")
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
    public function update(UpdateCategoryShowCaseRequest $request, string $id)
    {
        $data = CategoryShowcase::find($id);

        if (!$data) {
            return $this->error('CategoryShowcase not found', Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validated();
        $userId = request()->header('X-User-Id');
        $validatedData['updated_by'] = $userId;
        $data->update($validatedData);
    
        return $this->success(new CategoryShowCaseResource($data), 'CategoryShowcase updated', Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/category-showcases/{id}",
     *     summary="Delete an vendor",
     *     tags={"CategoryShowcases"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the vendor to delete",
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
     *         description="Successfully deleted vendor",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="CategoryShowcase deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/CategoryShowCaseResource")
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
        $data = CategoryShowcase::find($id);

        if (!$data) {
            return $this->error('CategoryShowcase not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new CategoryShowCaseResource($data), 'CategoryShowcase deleted successfully', Response::HTTP_OK);
    
    }
}
