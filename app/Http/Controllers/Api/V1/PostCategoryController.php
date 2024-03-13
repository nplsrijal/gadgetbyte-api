<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\PostCategoryCollection;
use App\Http\Resources\PostCategoryResource;
use App\Http\Requests\StorePostCategoryRequest;
use App\Http\Requests\UpdatePostCategoryRequest;

class PostCategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/post-categories",
     *     summary="Get a list of Post Categories",
     *     tags={"Post Categories"},
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/PostCategoryResource"))
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
            $query = PostCategory::query();
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
            $query = PostCategory::query();
       
            $query->where('parent_id',0);
            $query->orderBy('order_by');
            $data=$query->get();
            foreach ($data as $key => $menu) {
                $subQuery = PostCategory::query();
            
                $subQuery->where('parent_id', $menu->id)
                    ->orderBy('order_by');
            
                $submenus = $subQuery->get();
                
                // Assigning submenus to the current menu item
                $data[$key]->children = $submenus;
            }
        }
        return $this->success(new PostCategoryCollection($data));


        $usertypeId = $request->user_type_id;
       
         $query = PostCategory::query();
       
         $query->where('parent_id',0);
         $query->orderBy('order_by');
         $data=$query->get();
         foreach ($data as $key => $menu) {
            $subQuery = PostCategory::query();
        
            $subQuery->where('parent_id', $menu->id)
                ->orderBy('order_by');
        
            $submenus = $subQuery->get();
            
            // Assigning submenus to the current menu item
            $data[$key]->children = $submenus;
        }

 
         return $this->success(new PostCategoryCollection($data));
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
     *     path="/api/v1/post-categories",
     *     summary="Create a new Post Category",
     *     tags={"Post Categories"},
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
     *         description="Post Categories data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StorePostCategoryRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created post category",
     *         @OA\JsonContent(ref="#/components/schemas/PostCategoryResource")
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
    public function store(StorePostCategoryRequest $request)
    {
        $validated = $request->validated();
        $userId = request()->header('X-User-Id');
        $validated['created_by'] = $userId;
        $validated['parent_id']=((int)$validated['parent_id'] < 1) ? '0' : $validated['parent_id'];
        
        $data = PostCategory::create($validated);
       
        return $this->success(new PostCategoryResource($data), 'PostCategory created', Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/post-categories/{id}",
     *     summary="Get a specific post category",
     *     tags={"Post Categories"},
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
     *         @OA\JsonContent(ref="#/components/schemas/PostCategoryResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function show(string $id)
    {
        $data = PostCategory::find($id);

        if ($data) {
            return $this->success(new PostCategoryResource($data));
        } else {
            return $this->error('PostCategory not found', Response::HTTP_NOT_FOUND);
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
     *     path="/api/v1/post-categories/{id}",
     *     summary="Update an existing post category",
     *     tags={"Post Categories"},
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
     *         description="PostCategory data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdatePostCategoryRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated post category",
     *         @OA\JsonContent(ref="#/components/schemas/PostCategoryResource")
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
    public function update(UpdatePostCategoryRequest $request, string $id)
    {
        $data = PostCategory::find($id);

        if (!$data) {
            return $this->error('PostCategory not found', Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validated();
        $userId = request()->header('X-User-Id');
        $validatedData['updated_by'] = $userId;
        $data->update($validatedData);
    
        return $this->success(new PostCategoryResource($data), 'PostCategory updated', Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/post-categories/{id}",
     *     summary="Delete an post category",
     *     tags={"Post Categories"},
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
     *             @OA\Property(property="message", type="string", example="PostCategory deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/PostCategoryResource")
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
        $data = PostCategory::find($id);

        if (!$data) {
            return $this->error('PostCategory not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new PostCategoryResource($data), 'PostCategory deleted successfully', Response::HTTP_OK);
    
    }
}
