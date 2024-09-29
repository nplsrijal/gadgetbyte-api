<?php

namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Api\V1\Controller;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\CategoryShowcase;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\AttributeCollection;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\CategoryShowCaseCollection;
use App\Http\Resources\CategoryShowCaseResource;


class CategoryFilterController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/frontend/category-side-filters",
     *     summary="Get a list of Categories",
     *     tags={"Side Filter Categories"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search term for filtering by camera/laptop/mobile",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/PostCategoryResource"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * )
     */
    public function index(Request $request)
    {
        

        $query = Attribute::query();
       
            $query->where('name',$request->input('q'))->orwhere('slug', 'ilike', '%' . $request->input('q') . '%');
            $data=$query->get();
            foreach ($data as $key => $menu) {
                $subQuery = AttributeOption::query();
            
                $subQuery->where('attribute_id', $menu->id)
                    ->orderBy('id');
            
                $submenus = $subQuery->get();
                
                // Assigning submenus to the current menu item
                $data[$key]->children = $submenus;
            }
        return $this->success(new AttributeCollection($data));

    }

    /**
     * @OA\Get(
     *     path="/api/v1/frontend/category-showcases",
     *     summary="Get a list of CategoryShowcases",
     *     tags={"Frontend CategoryShowcases"},
     *     security={{"bearer_token": {}}},
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CategoryShowCaseResource"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * )
     */
    public function categorybar(Request $request)
    {
        $perPage=$request->per_page;
            if(empty($perPage)){
                $perPage=20;
            }
            $query = CategoryShowcase::query();
            $query->orderBy('order_by');

            if ($request->has('q')) {
                $searchTerm = strtoupper($request->input('q'));
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'ilike', '%' . $searchTerm . '%');


                });
            }


            $data = $query->paginate($perPage)->withPath($request->getPathInfo());
 
         return $this->success(new CategoryShowCaseCollection($data));

    }
}
