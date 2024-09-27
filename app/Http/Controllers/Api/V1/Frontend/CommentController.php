<?php

namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Api\V1\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Product;
use App\Models\Post;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\CommentReport;
use App\Models\User;


use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\StoreLikeRequest;
use App\Http\Requests\StoreCommentReportRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Http\Resources\CommentLikeResource;
use App\Http\Resources\CommentLikeCollection;
use App\Http\Resources\CommentReportResource;
use App\Http\Resources\CommentReportCollection;

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
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         description="To filter for specific loggedinuser/all (mine/all)",
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
    $userId = $request->header('X-User-Id');

    $perPage = $request->input('per_page', 20); // Default to 20 if not provided

    // Initialize the query for parent comments (those with parent_comment_id = 0)
    $query = Comment::query()
        ->where('parent_comment_id', 0) // Only get top-level comments
        ->withCount([
            'likes as likes_count' => function ($query) {
                $query->where('is_like', true);
            },
            'likes as dislikes_count' => function ($query) {
                $query->where('is_like', false);
            },
        ])
        ->with([
            'user', // Load the user for the parent comment
            'replies' => function ($query) {
                $query->withCount([
                    'likes as likes_count' => function ($query) {
                        $query->where('is_like', true);
                    },
                    'likes as dislikes_count' => function ($query) {
                        $query->where('is_like', false);
                    },
                ])
                ->with(['user']) // Load the user for each reply
                ->join('users', 'users.id', '=', 'comments.created_by')
                ->addSelect('comments.*', DB::raw("CONCAT(users.firstname, ' ', users.lastname) as author_name"));
            }
        ]);

    $query->join('users', 'users.id', '=', 'comments.created_by')
        ->addSelect('comments.*', DB::raw("CONCAT(users.firstname, ' ', users.lastname) as author_name"));

    // Filter by commentable_type (e.g., Post or Product) if provided
    if ($request->has('type')) {
        $type = $request->input('type');
        $query->where('commentable_type', $type === 'post' ? 'App\Models\Post' : 'App\Models\Product');
    }

    // Filter by commentable_id (specific post or product) if provided
    if ($request->has('id')) {
        $commentableId = $request->input('id');
        $query->where('commentable_id', $commentableId);
    }
    if ($request->has('filter') && $request->input('filter')=='mine') {
        $query->where('comments.created_by', $userId);
    }

    // Paginate the results
    $data = $query->paginate($perPage)->withPath($request->getPathInfo());

    // Return the paginated and filtered comments with replies nested
    //return $this->success(new CommentCollection($data));
                $userId = $request->header('X-User-Id');

                $likedArray = [];
                $dislikedArray = [];

                if ($userId && $request->has('id')) {
                    $user = User::find($userId);

                    if($request->input('type')=='post')
                    {
                        $likedArray = $user->likedCommentsForPost($request->input('id'))->pluck('comment_id')->toArray();
                        $dislikedArray = $user->dislikedCommentsForPost($request->input('id'))->pluck('comment_id')->toArray();
                    }
                    else
                    {
                        $likedArray = $user->likedCommentsForProduct($request->input('id'))->pluck('comment_id')->toArray();
                        $dislikedArray = $user->dislikedCommentsForProduct($request->input('id'))->pluck('comment_id')->toArray();
                    }
                    
                }

                // Add liked_array and disliked_array to the response data
                return $this->success([
                    'comment' => new CommentCollection($data),
                    'comment_liked_array' => $likedArray,
                    'comment_disliked_array' => $dislikedArray
                ]);
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
        
        $userId = $request->header('X-User-Id');


        // Find the post by ID
        $post = Post::find($postId);

        // Check if the [post] was not found
        if (!$post) {
            return response()->json(['error' => 'Post not found'], Response::HTTP_NOT_FOUND);
        }
        $comment = $post->comments()->create([
            'body' => $request->body,
            'created_by' => $userId,
            'parent_comment_id'=>($request->has('parent_id') && (int)$request->parent_id > 0)?$request->parent_id:0
        ]);
        return $this->success($comment, 'Comment Created Successfully', Response::HTTP_CREATED);

    }


    /**
     * @OA\Post(
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
    public function toggleLikeDislike(StoreLikeRequest $request, $id)
    {
        $userId = $request->header('X-User-Id');
        $validated = $request->validated();


        $comment = Comment::findOrFail($id);

        $data=[
            'user_id' => $userId,
            'comment_id' => $id,
            'is_like' => true 
        ];

        // Find the existing like or dislike by the user on this comment
        $existingLike = CommentLike::where('user_id', $userId)
                                    ->where('comment_id', $id)
                                    ->first();

        if ($existingLike) {
            // if ($existingLike->is_like) {
            //     // If it was a like, change to dislike
            //     $existingLike->is_like = false;
            //     $existingLike->save();
            //     $data['is_like']=false;
            //     return $this->success($data, 'Comment Disliked Successfully', Response::HTTP_CREATED);

            // } else {
            //     // If it was a dislike, change to like
            //     $existingLike->is_like = true;
            //     $existingLike->save();
            //     $data['is_like']=true;

            //     return $this->success($data, 'Comment Liked Successfully', Response::HTTP_CREATED);
            // }
                $existingLike->is_like = ($validated['is_like']=='none')?null:$validated['is_like'];
                $existingLike->save();
                $data['is_like']=$validated['is_like'];
                return $this->success($data, 'Comment Status Updated Successfully', Response::HTTP_CREATED);
        } else {
            $data['is_like']=($validated['is_like']=='none')?null:$validated['is_like'];

            CommentLike::create($data);
            return $this->success($data, 'Comment Liked Successfully', Response::HTTP_CREATED);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/frontend/comments/{id}/report",
     *     summary="Report Of Comments",
     *     tags={"Comment Reports"},
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CommentReportResource"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * )
     */
    public function reportComment(StoreCommentReportRequest $request, $commentId)
    {
        
        $userId = $request->header('X-User-Id');
        $validated = $request->validated();


        // Find the comment by ID
        $data = Comment::find($commentId);

        // Check if the comment was not found
        if (!$data) {
            return response()->json(['error' => 'Comment not found'], Response::HTTP_NOT_FOUND);
        }

        $validated['created_by'] = $userId;
        $validated['comment_id'] = $commentId;
        
        $data = CommentReport::create($validated);
        return $this->success(new CommentReportResource($data), 'Comment Report created', Response::HTTP_CREATED);


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
    public function destroy(Request $request,string $id)
    {
        $userId = $request->header('X-User-Id');

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
