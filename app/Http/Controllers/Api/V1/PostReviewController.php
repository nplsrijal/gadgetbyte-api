<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\PostReviewCollection;
use App\Http\Resources\PostReviewResource;
use App\Http\Requests\StorePostReviewRequest;
use App\Http\Requests\UpdatePostReviewRequest;
use App\Models\PostReview;
use App\Models\Post;

class PostReviewController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/v1/post-review",
     *     summary="Get a list of Post Reviews",
     *     tags={"Post Review"},
     *     security={{"bearer_token": {}}},
     *      @OA\Parameter(
     *         name="post_id",
     *         in="query",
     *         description="Post id",
     *         @OA\Schema(type="integer")
     *     ),
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
        $perPage=$request->per_page;
        if(empty($perPage)){
            $perPage=20;
        }
        $query = PostReview::query()->where('post_id',$request->post_id);
        if ($request->has('q')) {
            $searchTerm = strtoupper($request->input('q'));
            $query->where(function ($query) use ($searchTerm) {
                $query->where('review', 'ilike', '%' . $searchTerm . '%');


            });
        }

        $data = $query->paginate($perPage)->withPath($request->getPathInfo());
        return $this->success(new PostReviewCollection($data));
    }
     /**
     * @OA\Post(
     *     path="/api/v1/post-review",
     *     summary="Create a new Post Review",
     *     tags={"Post Review"},
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
     *         description="Post Review data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StorePostReviewRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created post",
     *         @OA\JsonContent(ref="#/components/schemas/PostReviewResource")
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
    public function store(StorePostReviewRequest $request)
    {
        $post = Post::find($request->post_id);

        if (!$post) {
            return $this->error('Post not found', Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validated();
        $userId = request()->header('X-User-Id');

        $insert_data=[];
        foreach ($validated['reviews'] as  $data) {
            
            $insert_data[] = [
                'review_id' => $data['review_id'],
                'title' => $data['title'],
                'post_id'=>$validated['post_id'],
                'review' => $data['review'],
                'created_by'=>$userId
            ];
        }

        PostReview::insert($insert_data);
        return $this->success(new PostReviewResource($insert_data), 'Post With Review Inserted', Response::HTTP_OK);

    }
    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/v1/post-review/{id}",
     *     summary="Update an existing post review",
     *     tags={"Post Review"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the post review to update",
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
     *         description="Post Review data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdatePostReviewRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated post review",
     *         @OA\JsonContent(ref="#/components/schemas/PostReviewResource")
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
    public function update(UpdatePostReviewRequest $request, string $id)
    {
        $data = PostReview::find($id);

        if (!$data) {
            return $this->error('Post Review not found', Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validated();
        $userId = request()->header('X-User-Id');
        $validatedData['updated_by'] = $userId;
        $data->update($validatedData);
    
        return $this->success(new PostReviewResource($data), 'Post Review updated', Response::HTTP_OK);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/post-review/{id}",
     *     summary="Delete an post review",
     *     tags={"Post Review"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the post review to delete",
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
     *         description="Successfully deleted post review",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post Review deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/PostReviewResource")
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
        $data = PostReview::find($id);

        if (!$data) {
            return $this->error('Post not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new PostReviewResource($data), 'Post Review deleted successfully', Response::HTTP_OK);
    
    }
}
