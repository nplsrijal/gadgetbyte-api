<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\PostTag;
use App\Models\PostWithCategory;
use App\Models\PostReview;
use App\Models\PostFaq;
use App\Models\PostLog;

use DB;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/posts",
     *     summary="Get a list of Posts",
     *     tags={"Posts"},
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
     *        @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Search post for filtering by status",
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/PostResource"))
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
        $query = Post::query();
        if ($request->has('q')) {
            $searchTerm = strtoupper($request->input('q'));
            $query->where(function ($query) use ($searchTerm) {
                $query->where('title', 'ilike', '%' . $searchTerm . '%');


            });
        }
        if ($request->has('category_id')) {
            $query->join('post_with_categories', 'posts.id', '=', 'post_with_categories.post_id')

                    ->where('category_id', '=', $request->input('category_id'));

        }
        if ($request->has('status')) {
            $query->where('status','=',$request->input('status'));
        }

        $data = $query->paginate($perPage)->withPath($request->getPathInfo());
        return $this->success(new PostCollection($data));
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
     *     path="/api/v1/posts",
     *     summary="Create a new Post",
     *     tags={"Posts"},
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
     *         description="Posts data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StorePostRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created post",
     *         @OA\JsonContent(ref="#/components/schemas/PostResource")
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
    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();
        $userId = request()->header('X-User-Id');
        $validated['created_by'] = $userId;

        // if(isset($validated['tags']))
        // {
        //     $tags=$validated['tags'];
        //     unset($validated['tags']);

        // }
        // else
        // {
        //     $tags=[];
        // }
        if(isset($validated['categories']))
        {
            $categories=$validated['categories'];
            unset($validated['categories']);

        }
        else
        {
            $categories=[];
        }
        if(isset($validated['reviews']))
        {
            $reviews=$validated['reviews'];
            unset($validated['reviews']);

        }
        else
        {
            $reviews=[];
        }
        if(isset($validated['faqs']))
        {
            $faqs=$validated['faqs'];
            unset($validated['faqs']);

        }
        else
        {
            $faqs=[];
        }

        
        
         // Begin database transaction
         DB::beginTransaction();

        $data = Post::create($validated);

        // if(count($tags)> 0)
        // {
        //     $insert_tags=[];
        //     foreach($tags as $tag)
        //     {
        //         $insert_tags[]=array('post_id'=>$data->id,'tag_id'=>$tag);
        //     }

        //     PostTag::insert($insert_tags);
        // }
        if(count($categories)> 0)
        {
            $insert_cat=[];
            foreach($categories as $cat)
            {
                $insert_cat[]=array('post_id'=>$data->id,'category_id'=>$cat);
            }

            PostWithCategory::insert($insert_cat);
        }

        if(count($reviews)>0)
        {
            $insert_data=[];
            foreach ($reviews as  $review_data) {
                
                $insert_data[] = [
                    'review_id' => $review_data['review_id'],
                    'title' => $review_data['title'],
                    'post_id'=>$data->id,
                    'review' => $review_data['review'],
                    //'description' => $review_data['description'],
                    'created_by'=>$userId
                ];
            }

            PostReview::insert($insert_data);

        }

        if(count($faqs)>0)
        {
            $insert_data=[];
            foreach ($faqs as  $faq_data) {
                
                $insert_data[] = [
                    'post_id'=>$data->id,
                    'question' => $faq_data['question'],
                    'answer' => $faq_data['answer'],
                    'created_by'=>$userId
                ];
            }

            PostFaq::insert($insert_data);

        }


         // Check database transaction
         $transactionStatus = DB::transactionLevel();

         if ($transactionStatus > 0) {
             // Database transaction success
             DB::commit();
             return $this->success(new PostResource($data), 'Post created', Response::HTTP_CREATED);
            } else {
             // Throw error
             throw new Exception('Could not save Post.', 1);
         }

       
   
    }

     /**
     * @OA\Get(
     *     path="/api/v1/posts/{id}",
     *     summary="Get a specific post",
     *     tags={"Posts"},
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
        $data = Post::with(['reviews', 'reviews.reviews', 'categories','categories.category'])
        ->join('users','users.id','=','posts.created_by')
        ->select('posts.*', DB::raw("CONCAT(users.firstname, ' ', users.lastname) as author_name"))
        ->find($id);
        
         if ($data) {
            $data->prices=$data->prices;
            //$data->post_tags=$data->post_tags;
            $data->categories=$data->categories;
            $data->reviews=$data->reviews;
            $data->faqs=$data->faqs;
            $data->logs=$data->logs;

            return $this->success(new PostResource($data));
        } else {
            return $this->error('Post not found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/v1/posts/{id}",
     *     summary="Update an existing post",
     *     tags={"Posts"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the post to update",
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
     *         description="Post data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdatePostRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated post",
     *         @OA\JsonContent(ref="#/components/schemas/PostResource")
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
    public function update(UpdatePostRequest $request, string $id)
    {
        $data = Post::find($id);

        if (!$data) {
            return $this->error('Post not found', Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validated();
        $userId = request()->header('X-User-Id');
        $validatedData['updated_by'] = $userId;

        // if(isset($validatedData['tags']))
        // {
        //     $tags=$validatedData['tags'];
        //     unset($validatedData['tags']);

        // }
        // else
        // {
        //     $tags=[];
        // }

        if(isset($validatedData['categories']))
        {
            $categories=$validatedData['categories'];
            unset($validatedData['categories']);

        }
        else
        {
            $categories=[];
        }
        
        if(isset($validatedData['reviews']))
        {
            $reviews=$validatedData['reviews'];
            unset($validatedData['reviews']);

        }
        else
        {
            $reviews=[];
        }
        
        if(isset($validatedData['faqs']))
        {
            $faqs=$validatedData['faqs'];
            unset($validatedData['faqs']);

        }
        else
        {
            $faqs=[];
        }
      

         // Begin database transaction
         DB::beginTransaction();
        $data->update($validatedData);

        // if(count($tags) > 0)
        // {
        //     $posttag_data=PostTag::where('post_id', $data->id);
        //     $posttag_data->update(['archived_by' => $userId]);
        //     $posttag_data->delete();

        //     $insert_tags=[];

            
        //     foreach($tags as $tag)
        //     {
        //         $insert_tags[]=array('post_id'=>$data->id,'tag_id'=>$tag);
        //     }

        //     PostTag::insert($insert_tags);
        // }
        if(count($categories) > 0)
        {
            $postcategory_data=PostWithCategory::where('post_id', $data->id);
            $postcategory_data->update(['archived_by' => $userId]);
            $postcategory_data->delete();

            $insert_cat=[];

            
            foreach($categories as $cat)
            {
                $insert_cat[]=array('post_id'=>$data->id,'category_id'=>$cat);
            }

            PostWithCategory::insert($insert_cat);
        }

        if(count($reviews) > 0)
        {
            $postreview_data=PostReview::where('post_id', $data->id);
            $postreview_data->update(['archived_by' => $userId]);
            $postreview_data->delete();
            $insert_data=[];
            foreach ($reviews as  $review_data) {
                
                $insert_data[] = [
                    'review_id' => $review_data['review_id'],
                    'title' => $review_data['title'],
                    'post_id'=>$data->id,
                    'review' => $review_data['review'],
                  //  'description' => $review_data['description'],
                    'created_by'=>$userId
                ];
            }

            PostReview::insert($insert_data);

        }

        if(count($faqs)>0)
        {
            $postfaq_data=PostFaq::where('post_id', $data->id);
            $postfaq_data->update(['archived_by' => $userId]);
            $postfaq_data->delete();
            $insert_data=[];
            foreach ($faqs as  $faq_data) {
                
                $insert_data[] = [
                    'post_id'=>$data->id,
                    'question' => $faq_data['question'],
                    'answer' => $faq_data['answer'],
                    'created_by'=>$userId
                ];
            }

            PostFaq::insert($insert_data);

        }

        $log_Data = [
            'post_id'=>$data->id,
            'type' => 'Update',
            'data_captured' => json_encode($request->validated()),
            'created_by'=>$userId
        ];

        PostLog::create($log_Data);


         // Check database transaction
         $transactionStatus = DB::transactionLevel();

         if ($transactionStatus > 0) {
             // Database transaction success
             DB::commit();
             return $this->success(new PostResource($data), 'Post updated', Response::HTTP_OK);
            } else {
             // Throw error
             throw new Exception('Could not save Post.', 1);
         }
    
    }

   /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/posts/{id}",
     *     summary="Delete an post",
     *     tags={"Posts"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the post to delete",
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
     *         description="Successfully deleted post",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/PostResource")
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
        $data = Post::find($id);

        if (!$data) {
            return $this->error('Post not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new PostResource($data), 'Post deleted successfully', Response::HTTP_OK);
    
    }
}
