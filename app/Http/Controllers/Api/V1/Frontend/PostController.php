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
     *         name="category_slug",
     *         in="query",
     *         description="Search post for filtering by catgory slug ",
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
         $query->join('users','users.id','=','posts.created_by')
         ->select('posts.*', DB::raw("CONCAT(users.firstname, ' ', users.lastname) as author_name"));

        if ($request->has('q')) {
            $searchTerm = strtoupper($request->input('q'));
            $query->where('title', 'ilike', '%' . $searchTerm . '%');
        }

        if ($request->has('category_id')) {
            $query->join('post_with_categories', 'posts.id', '=', 'post_with_categories.post_id')
                ->where('post_with_categories.category_id', '=', $request->input('category_id'));
        }
        if ($request->has('category_slug')) {
            $query->join('post_with_categories', 'posts.id', '=', 'post_with_categories.post_id')
                  ->join('post_categories', 'post_with_categories.category_id', '=', 'post_categories.id')
                ->where('post_categories.slug', '=', $request->input('category_slug'));
        }
        $query->orderBy('posts.created_at', 'desc');

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/v1/frontend/post/{slug}",
     *     summary="Get a specific post",
     *     tags={"Frontend Posts"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="The Slug of the post to retrieve",
     *         required=true,
     *         @OA\Schema(type="string")
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
    public function show(string $slug)
    {
        $data = Post::with(['reviews', 'reviews.reviews', 'categories','categories.category'])
        ->join('users','users.id','=','posts.created_by')
        ->select('posts.*', DB::raw("CONCAT(users.firstname, ' ', users.lastname) as author_name"))

        ->where('slug',$slug)->first();
        
         if ($data) {
            $data->prices=$data->prices;
            $data->faqs=$data->faqs;
            //$data->post_tags=$data->post_tags;
            $data->categories=$data->categories;
            $data->reviews=$data->reviews;
            return $this->success(new FrontendPostResource($data));
        } else {
            return $this->error('Post not found', Response::HTTP_NOT_FOUND);
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
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
