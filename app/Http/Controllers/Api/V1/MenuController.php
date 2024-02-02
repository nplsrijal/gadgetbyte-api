<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\MenuCollection;
use App\Http\Resources\MenuResource;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;


class MenuController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/menus",
     *     summary="Get a list of Menus",
     *     tags={"Menus"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search term for filtering by name or url ",
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/MenuResource"))
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
        $query = Menu::query();
        if ($request->has('q')) {
            $searchTerm = strtoupper($request->input('q'));
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'ilike', '%' . $searchTerm . '%')
                ->orwhere('url', 'ilike', '%' . $searchTerm . '%');


            });
        }

        $data = $query->paginate($perPage)->withPath($request->getPathInfo());
        return $this->success(new MenuCollection($data));
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
     *     path="/api/v1/menus",
     *     summary="Create a new Menu",
     *     tags={"Menus"},
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
     *         description="Menus data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreMenuRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created menu",
     *         @OA\JsonContent(ref="#/components/schemas/MenuResource")
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
    public function store(StoreMenuRequest $request)
    {
        $validated = $request->validated();
        $userId = request()->header('X-User-Id');
        $validated['created_by'] = $userId;
        $validated['parent_id']=((int)$validated['parent_id'] < 1) ? '0' : $validated['parent_id'];
        
        $data = Menu::create($validated);
       
        return $this->success(new MenuResource($data), 'Menu created', Response::HTTP_CREATED);
   
    }

     /**
     * @OA\Get(
     *     path="/api/v1/menus/{id}",
     *     summary="Get a specific menu",
     *     tags={"Menus"},
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
     *         @OA\JsonContent(ref="#/components/schemas/MenuResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function show(string $id)
    {
        $data = Menu::find($id);

        if ($data) {
            return $this->success(new MenuResource($data));
        } else {
            return $this->error('Menu not found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/v1/menus/{id}",
     *     summary="Update an existing menu",
     *     tags={"Menus"},
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
     *         description="Menu data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateMenuRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated menu",
     *         @OA\JsonContent(ref="#/components/schemas/MenuResource")
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
    public function update(UpdateMenuRequest $request, string $id)
    {
        $data = Menu::find($id);

        if (!$data) {
            return $this->error('Menu not found', Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validated();
        $userId = request()->header('X-User-Id');
        $validatedData['updated_by'] = $userId;
        $data->update($validatedData);
    
        return $this->success(new MenuResource($data), 'Menu updated', Response::HTTP_OK);
    }

   /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/menus/{id}",
     *     summary="Delete an menu",
     *     tags={"Menus"},
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
     *         description="Successfully deleted menu",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Menu deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/MenuResource")
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
        $data = Menu::find($id);

        if (!$data) {
            return $this->error('Menu not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new MenuResource($data), 'Menu deleted successfully', Response::HTTP_OK);
    
    }
}
