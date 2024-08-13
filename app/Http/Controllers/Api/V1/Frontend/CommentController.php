<?php

namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Api\V1\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Product;
use App\Models\Post;
use App\Models\Comment;
use App\Models\CommentLike;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Http\Resources\CommentLikeResource;
use App\Http\Resources\CommentLikeCollection;

use DB;

class CommentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/frontend/comments",
     *     summary="Get a list of Comments",
     *     tags={"Comments"},
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
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page (optional, default: 20)",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Type for post or for product",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Id of post or  product",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CommentResource"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * )
     */
    public function index(Request $request)
    {
        

        $perPage = $request->input('per_page', 20); // Default to 20 if not provided

        // Initialize the query for comments
        $query = Comment::query()
        ->withCount([
            'likes as likes_count' => function ($query) {
                $query->where('is_like', true);
            },
            'likes as dislikes_count' => function ($query) {
                $query->where('is_like', false);
            },
        ]);
        $query->join('users','users.id','=','comments.created_by')
        ->select('comments.*', DB::raw("CONCAT(users.firstname, ' ', users.lastname) as author_name"));

        // Apply search filter if 'q' parameter is provided
        // if ($request->has('q')) {
        //     $searchTerm = strtoupper($request->input('q'));
        //     $query->where(function ($query) use ($searchTerm) {
        //         $query->where('body', 'ilike', '%' . $searchTerm . '%');
        //     });
        // }

        // Filter by commentable_type (e.g., Post or Product) if provided
        if ($request->has('type')) {
            $type = $request->input('type');
            if($type=='post')
            {
                $query->where('commentable_type', 'App\Models\Post');

            }
            else if($type=='product')
            {
                $query->where('commentable_type', 'App\Models\Product');

            }
        }

        // Filter by commentable_id (specific post or product) if provided
        if ($request->has('id')) {
            $commentableId = $request->input('id');
            $query->where('commentable_id', $commentableId);
        }

        // Paginate the results
        $data = $query->paginate($perPage)->withPath($request->getPathInfo());

        // Return the paginated and filtered comments
        return $this->success(new CommentCollection($data));
    }


    /**
     * @OA\Post(
     *     path="/api/v1/frontend/product/{id}/comments",
     *     summary="Create a new Comment",
     *     tags={"Product Comments"},
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
     *         description="Comments data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreCommentRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created comment",
     *         @OA\JsonContent(ref="#/components/schemas/CommentResource")
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
    public function storeProductComment(StoreCommentRequest $request, $productId)
    {
        
        $userId = request()->header('X-User-Id');

        // Find the product by ID
        $product = Product::find($productId);

        // Check if the product was not found
        if (!$product) {
            return response()->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }
        $comment = $product->comments()->create([
            'body' => $request->body,
            'created_by' => $userId,
        ]);
        return $this->success($comment, 'Comment Created Successfully', Response::HTTP_CREATED);

    }

    /**
     * @OA\Post(
     *     path="/api/v1/frontend/post/{id}/comments",
     *     summary="Create a new Comment",
     *     tags={"Post Comments"},
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
     *         description="Comments data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreCommentRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created comment",
     *         @OA\JsonContent(ref="#/components/schemas/CommentResource")
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
    public function storePostComment(StoreCommentRequest $request, $postId)
    {
        
        $userId = request()->header('X-User-Id');

        // Find the post by ID
        $post = Post::find($postId);

        // Check if the [post] was not found
        if (!$post) {
            return response()->json(['error' => 'Post not found'], Response::HTTP_NOT_FOUND);
        }
        $comment = $post->comments()->create([
            'body' => $request->body,
            'created_by' => $userId,
        ]);
        return $this->success($comment, 'Comment Created Successfully', Response::HTTP_CREATED);

    }


    /**
     * @OA\Get(
     *     path="/api/v1/frontend/comments/{id}/toggle-like",
     *     summary="Like/Dislike Of Comments",
     *     tags={"Comments Like/Dislike"},
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
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CommentLikeResource"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * )
     */
    public function toggleLikeDislike(Request $request, $id)
    {
        $userId = request()->header('X-User-Id');

        $comment = Comment::findOrFail($id);

        // Find the existing like or dislike by the user on this comment
        $existingLike = CommentLike::where('user_id', $userId)
                                    ->where('comment_id', $id)
                                    ->first();

        if ($existingLike) {
            if ($existingLike->is_like) {
                // If it was a like, change to dislike
                $existingLike->is_like = false;
                $existingLike->save();
                return $this->success(new CommentLikeCollection($existingLike), 'Comment Disliked Successfully', Response::HTTP_CREATED);

            } else {
                // If it was a dislike, change to like
                $existingLike->is_like = true;
                $existingLike->save();
                return $this->success(new CommentLikeCollection($existingLike), 'Comment Liked Successfully', Response::HTTP_CREATED);
            }
        } else {
            // If no like or dislike exists, create a new like
            $data=[
                'user_id' => $userId,
                'comment_id' => $id,
                'is_like' => true // default to like
            ];
            CommentLike::create($data);
            return $this->success($data, 'Comment Liked Successfully', Response::HTTP_CREATED);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/comments/{id}",
     *     summary="Delete an Comment",
     *     tags={"Comments"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the brand to delete",
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
     *         description="Successfully deleted comment",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Comment deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/CommentResource")
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
        $userId = request()->header('X-User-Id');

        // $data = Comment::find($id);
        $data = Comment::where('created_by', $userId)
        ->where('id', $id)
        ->first();

        if (!$data) {
            return $this->error('Comment not found', Response::HTTP_NOT_FOUND);
        }

        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new CommentResource($data), 'Comment deleted successfully', Response::HTTP_OK);
    
    }
}
