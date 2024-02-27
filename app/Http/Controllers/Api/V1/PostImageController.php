<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\PostImageCollection;
use App\Http\Resources\PostImageResource;
use App\Http\Requests\StorePostImageRequest;
use App\Models\PostImage;
use App\Models\Post;

class PostImageController extends Controller
{
     /**
     * @OA\Post(
     *     path="/api/v1/post-image",
     *     summary="Create a new Post Image",
     *     tags={"Post Image"},
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
     *         description="Post Image data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StorePostImageRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created image",
     *         @OA\JsonContent(ref="#/components/schemas/PostImageResource")
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
    public function store(StorePostImageRequest $request)
    {
        $post = Post::find($request->post_id);

        if (!$post) {
            return $this->error('Post not found', Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validated();
        $userId = request()->header('X-User-Id');

        $insert_data=[];
        foreach ($validated['images'] as  $data) {
            
            $insert_data[] = [
                'title' => $data['title'],
                'post_id'=>$validated['post_id'],
                'slug' => $validated['slug'],
                'image' => $data['image'],
                'created_by'=>$userId
            ];
        }

        PostImage::insert($insert_data);
        return $this->success(new PostImageResource($insert_data), 'Post With Image Inserted', Response::HTTP_OK);

    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/post-image/{id}",
     *     summary="Delete an post image",
     *     tags={"Post Image"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the post image to delete",
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
     *         description="Successfully deleted post image",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post Image deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/PostImageResource")
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
        $data = PostImage::find($id);

        if (!$data) {
            return $this->error('Post not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new PostImageResource($data), 'Post Image deleted successfully', Response::HTTP_OK);
    
    }
}
