<?php

namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Api\V1\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\ProductCollection;
use App\Http\Resources\FrontendProductResource;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\ProductWithCategory;
use App\Models\ProductVariant;
use App\Models\ProductVariantVendor;
use App\Models\ProductImage;
use App\Models\ProductVideo;
use App\Models\ProductPost;
use App\Models\User;
use App\Models\ProductCategory;

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
     *       @OA\Parameter(
     *         name="multi_slugs",
     *         in="query",
     *         description="Search Product by using slugs (multiple) [eg: samsung-galaxy-s22;samsung-galaxy-s22-ultra;samsung-galaxy-s24]",
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
        $cat_Desc='';
        $cat_longdesc='';
        if($request->has('category_slug'))
        {
            $category=ProductCategory::select('description','long_description')->where('slug',$request->input('category_slug'))->first();
            $cat_Desc=$category->description;
            $cat_longdesc=$category->long_description;
        }

        $query = Product::join('users','users.id','=','products.created_by')
         ->select('products.*', DB::raw("CONCAT(users.firstname, ' ', users.lastname) as author_name"));
         $query->addSelect(DB::raw("'" . str_replace("'", "''", $cat_Desc) . "' as category_description"));
         $query->addSelect(DB::raw("'" . str_replace("'", "''", $cat_longdesc) . "' as category_long_description"));

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
        if($request->has('type')  && $request->input('type') == 'filter'){


            $query->join('product_attributes as pa', 'pa.product_id', '=', 'products.id')
                ->join('attribute_options as ao', 'ao.id', '=', 'pa.attribute_option_id')
                ->join('attributes as a', 'a.id', '=', 'ao.attribute_id');

                $default_field='pa.values';
                $filterMappings = [
                    'display' => ['slug' => 'display', 'field' => $default_field],        // For 'display', search in 'pa.values'
                    'cameras' => ['slug' => 'camera', 'field' => 'pa.attribute_name'], // For 'cameras', search in 'pa.attribute_name'
                ];
                foreach ($filterMappings as $param => $filter) {
                    // Check if the parameter is present in the request
                    if ($request->has($param)) {
                        // Get the values from the request and split by commas (if multiple values)
                        $values = array_map('trim', explode(',', $request->input($param)));
                
                        // Ensure the query checks the appropriate slug (or other field)
                        $query->whereRaw("LOWER(a.slug) ILIKE ?", ['%' . strtolower($filter['slug']) . '%']);
                
                        // Apply the filter logic for multiple values
                        if (count($values) > 1) {
                            $query->where(function ($q) use ($values, $filter) {
                                foreach ($values as $value) {
                                    $q->orWhereRaw("{$filter['field']}::text ILIKE ?", ['%' . strtolower($value) . '%']);
                                }
                            });
                        } else {
                            // Single value, apply simple ILIKE
                            $query->whereRaw("{$filter['field']}::text ILIKE ?", ['%' . strtolower($values[0]) . '%']);
                        }
                    }
                }
        
            
       }

        // Handle multi_slugs filter
        if ($request->has('multi_slugs')) {
            // Split the slugs by `;` and trim whitespace
            $slugs = array_map('trim', explode(';', $request->input('multi_slugs')));

            // Add whereIn condition for slugs
            $query->whereIn('products.slug', $slugs);
        }
        $query->groupBy('products.id', 'products.title','products.slug','users.firstname', 'users.lastname');

        $query->orderBy('products.created_at', 'desc');

        // DB::enableQueryLog();
        // $data = $query->get();
        // $lastQuery = DB::getQueryLog();
        // $sql = end($lastQuery);
        // echo $sql['query']; // SQL query
        // echo json_encode($sql['bindings']); // Parameters
        // exit;

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
    
    public function show__(Request $request, string $slug)
    {
        // as all data with table join became slow so api is breakdown and created next function

        // Check if multiple slugs are provided
        $isMulti = strpos($slug, ',') !== false;
    
        // Start building the query with relationships
        $data = Product::with([
            'categories', 
            //'categories.category',
          //  'variations',
            'images',
            'videos',
           // 'variants',
           // 'variants.variantAttributes', 
           // 'variants.variantVendors.vendor',
           // 'product_specifications',
           // 'product_specifications.specification'
        ])
        ->join('users', 'users.id', '=', 'products.created_by')
        ->select('products.*', 'users.email', 'users.facebook_url', 'users.instagram_url', 'users.linkedin_url', 'users.google_url', 'users.twitter_url', 'users.youtube_url', 
            DB::raw("CONCAT(users.firstname, ' ', users.lastname) as author_name, users.description as author_description"));
    
        // If multiple slugs are passed, handle whereIn query
        if ($isMulti) {
            $slugs = array_map('trim', explode(',', $slug));
            $data = $data->whereIn('products.slug', $slugs);
        } else {
            // Single slug query
            $data = $data->where('products.slug', $slug);
        }
    
        // Fetch the result
        $data = $data->orderBy('products.created_at', 'desc')->get();
        // $sql=$data->toSql();
        //  echo $sql;exit;
    
        if ($data->isEmpty()) {
            return $this->error('Product not found', Response::HTTP_NOT_FOUND);
        }
    
        foreach ($data as $index => $product) {
            $attributes = DB::table('product_attributes as pa')
                ->join('attribute_options as ao', 'pa.attribute_option_id', '=', 'ao.id')
                ->join('attributes as a', 'a.id', '=', 'ao.attribute_id')
                ->where('pa.product_id', $product->id)
                ->select('a.id', 'a.name')
                ->distinct()
                ->get();
    
            foreach ($attributes as $key => $li) {
                $attributes[$key]->attributes = DB::table('product_attributes as pa')
                    ->join('attribute_options as ao', 'pa.attribute_option_id', '=', 'ao.id')
                    ->join('attributes as a', 'a.id', '=', 'ao.attribute_id')
                    ->where('pa.product_id', $product->id)
                    ->where('a.id', $li->id)
                    ->select('pa.id', 'pa.attribute_name as name', 'pa.values')
                    ->get();
            }
            $data[$index]->attribute_sets = $attributes;
    
            $posts = DB::table('posts as p')
                ->join('product_posts as pp', 'p.id', '=', 'pp.post_id')
                ->where('pp.product_id', $product->id)
                ->where('p.status', 'P')
                ->where('p.archived_by', null)
                ->select('p.title', 'p.slug', 'p.created_at', 'p.featured_image')
                ->distinct()
                ->get();
    
            $data[$index]->related_posts = $posts;
        }
    
        // If only a single product was requested, return the first element of the array
        if (!$isMulti) {
            $data = $data->first();  // Change this line to return the first element
        }
    
        return $this->success(new FrontendProductResource($data));
    }
    public function show(Request $request, string $slug)
    {

        if($request->input('type')=='product')
        {
            $data=$this->getProductInformation($slug);
        }
        else if($request->input('type')=='specification')
        {
            $data=$this->getProductSpecification($slug);
        }
        else if($request->input('type')=='variants')
        {
            $data=$this->getProductVariants($slug);
        }
        else if($request->input('type')=='related_information')
        {
            $data=$this->getProductRelatedInformation($slug);
        }
        else
        {
            $data=collect(); 

        }


        if ($data->isEmpty()) {
            return $this->error('Product not found', Response::HTTP_NOT_FOUND);
        }
        else
        {
            return $this->success(new FrontendProductResource($data));


        }
    }
    function getProductInformation($slug)
    {
        // Check if multiple slugs are provided
        $isMulti = strpos($slug, ',') !== false;
    
        // Start building the query with relationships
        $data = Product::with([
            'categories.category:id,name,slug',
            'images',
            'videos',
            'product_specifications',
            'product_specifications.specification'
            
        ])
        ->join('users', 'users.id', '=', 'products.created_by')
        ->select('products.*', 'users.email', 'users.facebook_url', 'users.instagram_url', 'users.linkedin_url', 'users.google_url', 'users.twitter_url', 'users.youtube_url', 
            DB::raw("CONCAT(users.firstname, ' ', users.lastname) as author_name, users.description as author_description"));
    
        // If multiple slugs are passed, handle whereIn query
        if ($isMulti) {
            $slugs = array_map('trim', explode(',', $slug));
            $data = $data->whereIn('products.slug', $slugs);
        } else {
            // Single slug query
            $data = $data->where('products.slug', $slug);
        }
    
        // Fetch the result
        $data = $data->orderBy('products.created_at', 'desc')->get();
        return $data;
       

    }
    function getProductSpecification($slug)
    {
        $isMulti = strpos($slug, ',') !== false;

        if ($isMulti) {
            $slugs = array_map('trim', explode(',', $slug));
            $productIds = Product::whereIn('products.slug', $slugs)->pluck('id');
        } else {
            // Single slug query
            $productIds = Product::where('products.slug', $slug)->pluck('id');
        }

        $specifications = collect(); // Initialize a collection

        foreach ($productIds as $productId) {
            $attributes = DB::table('product_attributes as pa')
                ->join('attribute_options as ao', 'pa.attribute_option_id', '=', 'ao.id')
                ->join('attributes as a', 'a.id', '=', 'ao.attribute_id')
                ->where('pa.product_id', $productId)
                ->select('a.id', 'a.name')
                ->distinct()
                ->get();

            foreach ($attributes as $key => $li) {
                $li->attributes = DB::table('product_attributes as pa')
                    ->join('attribute_options as ao', 'pa.attribute_option_id', '=', 'ao.id')
                    ->join('attributes as a', 'a.id', '=', 'ao.attribute_id')
                    ->where('pa.product_id', $productId)
                    ->where('a.id', $li->id)
                    ->select('pa.id', 'pa.attribute_name as name', 'pa.values')
                    ->get();
            }

            // $highlighted_specs=ProductSpecification::select('name','image','values')
            //                    ->join('specifications as s','s.id','=','product_specifications.specification_id')
            //                    ->where('product_id',$productId)->get();


            // Wrap the result in an object instead of an array
            $specifications->push((object) [
                'product_id' => $productId,
                'specifications' => $attributes,
               // 'highlighted_specifications'=>$highlighted_specs
            ]);
        }

        return $specifications; 
    }
    function getProductVariants($slug)
    {
        $isMulti = strpos($slug, ',') !== false;

        if ($isMulti) {
            $slugs = array_map('trim', explode(',', $slug));
            $productIds = Product::whereIn('products.slug', $slugs)->pluck('id');
        } else {
            // Single slug query
            $productIds = Product::where('products.slug', $slug)->pluck('id');
        }

        $specifications = collect(); // Initialize a collection

        foreach ($productIds as $productId) {
            $attributes = ProductVariant::where('product_id', $productId)
                ->get();

            foreach ($attributes as $key => $li) {
                $li->vendors =  ProductVariantVendor::join('vendors as  v','v.id','=','product_variant_vendors.vendor_id')
                ->where('variant_slug', $li->slug)
                ->where('product_id', $productId)
                ->select('product_url', 'name')
                ->get();
               
            }

            //$images = ProductImage::where('product_id', $productId)->get();

           

            // Wrap the result in an object instead of an array
            $specifications->push((object) [
                'product_id' => $productId,
                'variants' => $attributes,
               // 'images' => $images,
            ]);
        }

        return $specifications; 

    }

    function getProductRelatedInformation($slug)
    {
        $isMulti = strpos($slug, ',') !== false;

        if ($isMulti) {
            $slugs = array_map('trim', explode(',', $slug));
            $productIds = Product::whereIn('products.slug', $slugs)->pluck('id');
        } else {
            // Single slug query
            $productIds = Product::where('products.slug', $slug)->pluck('id');
        }

        $specifications = collect(); // Initialize a collection

        foreach ($productIds as $productId) {
            // $videos = ProductVideo::where('product_id', $productId)
            //     ->get();

             $posts=ProductPost::join('posts as p','p.id','=','product_posts.post_id')
                ->where('product_id',$productId)
                ->where('p.status', 'P')
                ->where('p.archived_by', null)
                ->select('p.title', 'p.slug', 'p.created_at', 'p.featured_image')
                ->get();

           

            // Wrap the result in an object instead of an array
            $specifications->push((object) [
                'product_id' => $productId,
                //'videos' => $videos,
                'posts' => $posts,
            ]);
        }

        return $specifications; 
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
