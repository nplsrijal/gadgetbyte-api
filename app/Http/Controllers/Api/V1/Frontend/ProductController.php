<?php

namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Api\V1\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\ProductCollection;
use App\Http\Resources\FrontendProductResource;
use App\Models\Product;
use App\Models\ProductWithCategory;
use App\Models\User;

use DB;

class ProductController extends Controller
{
   /**
     * @OA\Get(
     *     path="/api/v1/frontend/product",
     *     summary="Get a list of products",
     *     tags={"Frontend products"},
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
     *         description="Search Product for filtering by catgory id ",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="category_slug",
     *         in="query",
     *         description="Search Product for filtering by catgory slug ",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *        @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Search Product for filtering by status",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *       @OA\Parameter(
     *         name="author",
     *         in="query",
     *         description="Search Product for filtering by author",
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/FrontendProductResource"))
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

        $query = Product::with(['categories', 'categories.category']);
         $query->join('users','users.id','=','products.created_by')
         ->select('products.*', DB::raw("CONCAT(users.firstname, ' ', users.lastname) as author_name"));

        if ($request->has('q')) {
            $searchTerm = strtoupper($request->input('q'));
            $query->where('title', 'ilike', '%' . $searchTerm . '%');
        }

        if ($request->has('category_id')) {
            $query->join('product_with_categories', 'products.id', '=', 'product_with_categories.product_id')
                ->where('product_with_categories.category_id', '=', $request->input('category_id'));
        }
        if ($request->has('category_slug')) {
            $query->join('product_with_categories', 'products.id', '=', 'product_with_categories.product_id')
                  ->join('product_categories', 'product_with_categories.category_id', '=', 'product_categories.id')
                ->where('product_categories.slug', '=', $request->input('category_slug'));
        }
        if ($request->has('status')) {
            $query->where('status','=',$request->input('status'));
        }
        if ($request->has('author')) {
            $query->where('users.email','=',$request->input('author'));
        }
        $query->orderBy('products.created_at', 'desc');

        // $sql=$query->toSql();
        // echo $sql;exit;
        $data = $query->paginate($perPage);

        if ($data->count() > 0) {
            return $this->success(new ProductCollection($data));
        } else {
            return $this->error('Product not found', Response::HTTP_NOT_FOUND);
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
     *     path="/api/v1/frontend/Product/{slug}",
     *     summary="Get a specific Product",
     *     tags={"Frontend products"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="The Slug of the Product to retrieve",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function show(Request $request,string $slug)
    {
        $data = Product::with([
            'categories', 
            'categories.category',
            'attributes',
            'variations',
            'images',
            'variants',
            'variants.variantAttributes', 
            'variants.variantVendors.vendor'     
        ])
        ->join('users','users.id','=','products.created_by')
        ->select('products.*','users.email','users.facebook_url','users.instagram_url','users.linkedin_url','users.google_url','users.twitter_url','users.youtube_url', DB::raw("CONCAT(users.firstname, ' ', users.lastname) as author_name,users.description as author_description"))

        ->where('slug',$slug)->first();
        
         if ($data) {
           
           
            return $this->success(new FrontendProductResource($data));
           // Get the authenticated user ID from the header
                // $userId = $request->header('X-User-Id');

                // $likedArray = [];
                // $dislikedArray = [];

                // if ($userId) {
                //     $user = User::find($userId);

                //     $likedArray = $user->likedCommentsForProduct($data->id)->pluck('comment_id')->toArray();
                //     $dislikedArray = $user->dislikedCommentsForProduct($data->id)->pluck('comment_id')->toArray();
                // }

                // // Add liked_array and disliked_array to the response data
                // return $this->success([
                //     'product' => new FrontendProductResource($data),
                //     'comment_liked_array' => $likedArray,
                //     'comment_disliked_array' => $dislikedArray
                // ]);
        } else {
            return $this->error('Product not found', Response::HTTP_NOT_FOUND);
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
