<?php

namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Api\V1\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\PostCollection;
use App\Http\Resources\FrontendPostResource;
use App\Models\Post;
use App\Models\PostTag;
use App\Models\PostWithCategory;
use App\Models\PostReview;

use DB;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/frontend/post",
     *     summary="Get a list of Posts",
     *     tags={"Frontend Posts"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search term for filtering by name ",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Search post for filtering by catgory id ",
     *         required=false,
     *         @OA\Schema(type="integer")
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/FrontendPostResource"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?? 20;

        $query = Post::with(['categories', 'categories.category']);
        
        if ($request->has('q')) {
            $searchTerm = strtoupper($request->input('q'));
            $query->where('title', 'ilike', '%' . $searchTerm . '%');
        }

        if ($request->has('category_id')) {
            $query->join('post_with_categories', 'posts.id', '=', 'post_with_categories.post_id')
                ->where('post_with_categories.category_id', '=', $request->input('category_id'));
        }

        // $sql=$query->toSql();
        // echo $sql;exit;
        $data = $query->paginate($perPage);

        if ($data->count() > 0) {
            return $this->success(new PostCollection($data));
        } else {
            return $this->error('Post not found', Response::HTTP_NOT_FOUND);
        }
    }

     /**
     * @OA\Get(
     *     path="/api/v1/frontend/post/{id}",
     *     summary="Get a specific post",
     *     tags={"Frontend Posts"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the post to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/PostResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function show(string $id)
    {
        $data = Post::with(['reviews', 'reviews.reviews', 'categories','categories.category'])->find($id);
        
         if ($data) {
            $data->prices=$data->prices;
            //$data->post_tags=$data->post_tags;
            $data->categories=$data->categories;
            $data->reviews=$data->reviews;
            return $this->success(new PostResource($data));
        } else {
            return $this->error('Post not found', Response::HTTP_NOT_FOUND);
        }
    }

}
