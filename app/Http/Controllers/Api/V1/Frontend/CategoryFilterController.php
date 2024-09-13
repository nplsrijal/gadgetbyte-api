<?php

namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\AttributeCollection;
use App\Http\Resources\AttributeResource;


class CategoryFilterController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/category-side-filters",
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
       
            $query->where('name',$request->input('q'));
            $query->orderBy('order_by');
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
}
