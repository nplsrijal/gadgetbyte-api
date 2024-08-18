<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\BlindCameraTest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\BlindCameraTestCollection;
use App\Http\Resources\BlindCameraTestResource;
use App\Http\Requests\StoreBlindCameraTestRequest;
use App\Http\Requests\UpdateBlindCameraTestRequest;


class BlindCameraTestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/blindcamera-tests",
     *     summary="Get a list of BlindCameraTests",
     *     tags={"BlindCameraTests"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search term for filtering by name  ",
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/BlindCameraTestResource"))
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
        $query = BlindCameraTest::query();
        if ($request->has('q')) {
            $searchTerm = strtoupper($request->input('q'));
            $query->where(function ($query) use ($searchTerm) {
                $query->where('product_a_title', 'ilike', '%' . $searchTerm . '%');

            });
        }

        $data = $query->paginate($perPage)->withPath($request->getPathInfo());
        return $this->success(new BlindCameraTestCollection($data));
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
     *     path="/api/v1/blindcamera-tests",
     *     summary="Create a new BlindCameraTest",
     *     tags={"BlindCameraTests"},
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
     *         description="BlindCameraTests data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreBlindCameraTestRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created menu",
     *         @OA\JsonContent(ref="#/components/schemas/BlindCameraTestResource")
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
    public function store(StoreBlindCameraTestRequest $request)
    {
        $validated = $request->validated();
        $userId = request()->header('X-User-Id');
        $validated['created_by'] = $userId;
        $validated['product_a_images'] = json_encode($validated['product_a_images']);
        $validated['product_b_images'] = json_encode($validated['product_b_images']);
        
        $data = BlindCameraTest::create($validated);
       
        return $this->success(new BlindCameraTestResource($data), 'BlindCameraTest created', Response::HTTP_CREATED);
   
    }

     /**
     * @OA\Get(
     *     path="/api/v1/blindcamera-tests/{id}",
     *     summary="Get a specific menu",
     *     tags={"BlindCameraTests"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the menu to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/BlindCameraTestResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function show(string $id)
    {
        $data = BlindCameraTest::find($id);

        if ($data) {
            return $this->success(new BlindCameraTestResource($data));
        } else {
            return $this->error('BlindCameraTest not found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlindCameraTest $menu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/v1/blindcamera-tests/{id}",
     *     summary="Update an existing menu",
     *     tags={"BlindCameraTests"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the menu to update",
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
     *         description="BlindCameraTest data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateBlindCameraTestRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated menu",
     *         @OA\JsonContent(ref="#/components/schemas/BlindCameraTestResource")
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
    public function update(UpdateBlindCameraTestRequest $request, string $id)
    {
        $data = BlindCameraTest::find($id);

        if (!$data) {
            return $this->error('BlindCameraTest not found', Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validated();
        $userId = request()->header('X-User-Id');
        $validatedData['updated_by'] = $userId;
        $validatedData['product_a_images'] = json_encode($validatedData['product_a_images']);
        $validatedData['product_b_images'] = json_encode($validatedData['product_b_images']);
        
        $data->update($validatedData);
    
        return $this->success(new BlindCameraTestResource($data), 'BlindCameraTest updated', Response::HTTP_OK);
    }

   /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/blindcamera-tests/{id}",
     *     summary="Delete an menu",
     *     tags={"BlindCameraTests"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the menu to delete",
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
     *         description="Successfully deleted data",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="BlindCameraTest deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/BlindCameraTestResource")
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
        $data = BlindCameraTest::find($id);

        if (!$data) {
            return $this->error('BlindCameraTest not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new BlindCameraTestResource($data), 'BlindCameraTest deleted successfully', Response::HTTP_OK);
    
    }
}
