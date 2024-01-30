<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\UserTypeCollection;
use App\Http\Resources\UserTypeResource;
use App\Http\Requests\StoreUserTypeRequest;
use App\Http\Requests\UpdateUserTypeRequest;


use App\Models\UserType;


class UserTypeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/usertypes",
     *     summary="Get a list of UserTypes",
     *     tags={"UserTypes"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search term for filtering by name or code",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/UserTypeResource"))
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
        $query = UserType::query();
        if ($request->has('q')) {
            $searchTerm = strtoupper($request->input('q'));
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'ilike', '%' . $searchTerm . '%');
            });
        }

        $usertypes = $query->paginate($perPage)->withPath($request->getPathInfo());
        return $this->success(new UserTypeCollection($usertypes));
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
     *     path="/api/v1/usertypes",
     *     summary="Create a new UserType",
     *     tags={"UserTypes"},
     *     security={{"bearer_token": {}}},
     *     @OA\RequestBody(
     *         description="User Types data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreUserTypeRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created usertype",
     *         @OA\JsonContent(ref="#/components/schemas/UserTypeResource")
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
    public function store(StoreUserTypeRequest $request)
    {
        $validated = $request->validated();
        $userId = request()->header('X-User-Id');
        $validated['created_by'] = $userId;
        $usertype = UserType::create($validated);
       
        return $this->success(new UserTypeResource($usertype), 'User Type created', Response::HTTP_CREATED);
    }

   /**
     * @OA\Get(
     *     path="/api/v1/usertypes/{id}",
     *     summary="Get a specific usertypes",
     *     tags={"UserTypes"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the usertype to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UserTypeResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function show(string $id)
    {
        $usertype = UserType::find($id);

        if ($usertype) {
            return $this->success(new UserTypeResource($usertype));
        } else {
            return $this->error('User Type not found', Response::HTTP_NOT_FOUND);
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
     *     path="/api/v1/usertypes/{id}",
     *     summary="Update an existing usertype",
     *     tags={"UserTypes"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the usertype to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         description="UserType data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateUserTypeRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated usertype",
     *         @OA\JsonContent(ref="#/components/schemas/UserTypeResource")
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
    public function update(UpdateUserTypeRequest $request, string $id)
    {
        $usertype = UserType::find($id);

        if (!$usertype) {
            return $this->error('UserType not found', Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validated();
        $userId = request()->header('X-User-Id');
        $validatedData['updated_by'] = $userId;
        $usertype->update($validatedData);
    
        return $this->success(new usertypeResource($usertype), 'User Type updated', Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/usertypes/{id}",
     *     summary="Delete an UserType",
     *     tags={"UserTypes"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the UserType to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully deleted UserType",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="UserType deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserTypeResource")
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
        $usertype = UserType::find($id);

        if (!$usertype) {
            return $this->error('UserType not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $usertype->update(['archived_by' => $userId]);
        $usertype->delete();
        return $this->success(new UserTypeResource($usertype), 'UserType deleted successfully', Response::HTTP_OK);
    
    }
}
