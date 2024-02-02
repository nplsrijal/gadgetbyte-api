<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\MenuPermission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\PermissionCollection;
use App\Http\Resources\PermissionResource;
use App\Http\Requests\StorePermissionRequest;

class PermissionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/menu-permissions",
     *     summary="Get a list of Menu Permissions",
     *     tags={"Menu Permissions"},
     *     security={{"bearer_token": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/PermissionResource"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * )
     */
    public function index()
    {
       
        $data = MenuPermission::all();
        return $this->success(new PermissionCollection($data));
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
     *     path="/api/v1/menu-permissions",
     *     summary="Create a new Menu Permission",
     *     tags={"Menu Permissions"},
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
     *         description="Menu Permissions data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StorePermissionRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created menu permission",
     *         @OA\JsonContent(ref="#/components/schemas/PermissionResource")
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
    public function store(StorePermissionRequest $request)
    {
        $validated = $request->validated();
        $userId = request()->header('X-User-Id');
        $validated['created_by'] = $userId;
        
        $data = MenuPermission::create($validated);
       
        return $this->success(new PermissionResource($data), 'Permission created', Response::HTTP_CREATED);
   
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/menu-permissions/{id}",
     *     summary="Delete an menu permission",
     *     tags={"Menu Permissions"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the menu permisison to delete",
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
     *         description="Successfully deleted menu permission",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Menu Permission deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/PermissionResource")
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
        $data = MenuPermission::find($id);

        if (!$data) {
            return $this->error('Menu Permission not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new PermissionResource($data), 'Menu Permission deleted successfully', Response::HTTP_OK);
    
    }
}
