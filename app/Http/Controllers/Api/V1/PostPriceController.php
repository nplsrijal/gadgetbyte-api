<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\PostPriceCollection;
use App\Http\Resources\PostPriceResource;
use App\Http\Requests\StorePostPriceRequest;
use App\Http\Requests\UpdatePostPriceRequest;
use App\Models\PostPrice;
use App\Models\Post;

class PostPriceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/post-price",
     *     summary="Get a list of Post Price",
     *     tags={"Post Price"},
     *     security={{"bearer_token": {}}},
     *      @OA\Parameter(
     *         name="post_id",
     *         in="query",
     *         description="Post id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/PostReviewResource"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $perPage=20;
        $query = PostPrice::query()->where('post_id',$request->post_id);
        $data = $query->paginate($perPage)->withPath($request->getPathInfo());
        return $this->success(new PostPriceCollection($data));
    }
     /**
     * @OA\Post(
     *     path="/api/v1/post-price",
     *     summary="Create a new Post Price",
     *     tags={"Post Price"},
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
     *         description="Post Price data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StorePostPriceRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created post",
     *         @OA\JsonContent(ref="#/components/schemas/PostPriceResource")
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
    public function store(StorePostPriceRequest $request)
    {
        $post = Post::find($request->post_id);

        if (!$post) {
            return $this->error('Post not found', Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validated();
        $userId = request()->header('X-User-Id');

        $insert_data=[];
        foreach ($validated['price'] as  $data) {
            
            $insert_data[] = [
                'title' => $data['title'],
                'post_id'=>$validated['post_id'],
                'price' => $data['amount'],
                'created_by'=>$userId
            ];
        }

        PostPrice::insert($insert_data);
        return $this->success(new PostPriceResource($insert_data), 'Post With Price Inserted', Response::HTTP_OK);

    }
    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/v1/post-price/{id}",
     *     summary="Update an existing post price",
     *     tags={"Post Price"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the post price to update",
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
     *         description="Post Price data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdatePostPriceRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated post price",
     *         @OA\JsonContent(ref="#/components/schemas/PostPriceResource")
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
    public function update(UpdatePostPriceRequest $request, string $id)
    {
        $data = PostPrice::find($id);

        if (!$data) {
            return $this->error('Post Price not found', Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validated();
        $userId = request()->header('X-User-Id');
        $validatedData['updated_by'] = $userId;
        $data->update($validatedData);
    
        return $this->success(new PostPriceResource($data), 'Post Price updated', Response::HTTP_OK);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/post-price/{id}",
     *     summary="Delete an post price",
     *     tags={"Post Price"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the post price to delete",
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
     *         description="Successfully deleted post price",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post Price deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/PostPriceResource")
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
        $data = PostPrice::find($id);

        if (!$data) {
            return $this->error('Post not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new PostPriceResource($data), 'Post Price deleted successfully', Response::HTTP_OK);
    
    }
}
