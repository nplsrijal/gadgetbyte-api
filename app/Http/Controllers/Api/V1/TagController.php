<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\TagCollection;
use App\Http\Resources\TagResource;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;


class TagController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/tags",
     *     summary="Get a list of Tags",
     *     tags={"Tags"},
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/TagResource"))
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
        $query = Tag::query();
        if ($request->has('q')) {
            $searchTerm = strtoupper($request->input('q'));
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'ilike', '%' . $searchTerm . '%');


            });
        }

        $data = $query->paginate($perPage)->withPath($request->getPathInfo());
        return $this->success(new TagCollection($data));
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
     *     path="/api/v1/tags",
     *     summary="Create a new Tag",
     *     tags={"Tags"},
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
     *         description="Tags data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreTagRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created tag",
     *         @OA\JsonContent(ref="#/components/schemas/TagResource")
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
    public function store(StoreTagRequest $request)
    {
        $validated = $request->validated();
        $userId = request()->header('X-User-Id');
        $validated['created_by'] = $userId;
        
        $data = Tag::create($validated);
       
        return $this->success(new TagResource($data), 'Tag created', Response::HTTP_CREATED);
   
    }

     /**
     * @OA\Get(
     *     path="/api/v1/tags/{id}",
     *     summary="Get a specific tag",
     *     tags={"Tags"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the tag to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/TagResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function show(string $id)
    {
        $data = Tag::find($id);

        if ($data) {
            return $this->success(new TagResource($data));
        } else {
            return $this->error('Tag not found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/v1/tags/{id}",
     *     summary="Update an existing tag",
     *     tags={"Tags"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the tag to update",
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
     *         description="Tag data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateTagRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated tag",
     *         @OA\JsonContent(ref="#/components/schemas/TagResource")
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
    public function update(UpdateTagRequest $request, string $id)
    {
        $data = Tag::find($id);

        if (!$data) {
            return $this->error('Tag not found', Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validated();
        $userId = request()->header('X-User-Id');
        $validatedData['updated_by'] = $userId;
        $data->update($validatedData);
    
        return $this->success(new TagResource($data), 'Tag updated', Response::HTTP_OK);
    }

   /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/tags/{id}",
     *     summary="Delete an tag",
     *     tags={"Tags"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the tag to delete",
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
     *         description="Successfully deleted tag",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Tag deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/TagResource")
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
        $data = Tag::find($id);

        if (!$data) {
            return $this->error('Tag not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new TagResource($data), 'Tag deleted successfully', Response::HTTP_OK);
    
    }
}
