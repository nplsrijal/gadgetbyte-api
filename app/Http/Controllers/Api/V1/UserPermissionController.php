<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Menu;
use App\Http\Resources\MenuCollection;
use App\Http\Resources\MenuResource;
use DB;



class UserPermissionController extends Controller
{
     /**
     * @OA\Get(
     *     path="/api/v1/user-menus",
     *     summary="Get a list of user wise menu ",
     *     tags={"User Type Wise Menu"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="user_type_id",
     *         in="query",
     *         description="Filter by user_type_id (optional)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/MenuResource"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * )
     */


     public function index(Request $request)
     {
         $usertypeId = $request->user_type_id;
       
         $query = Menu::select('menus.id','menus.name','menus.url','menus.icon')->distinct()
         ->join('menu_permissions as mp','mp.menu_id','menus.id');
         
         //optional: if request is made through 
         if ($request->has('user_type_id')) {
             $query->where('user_type_id', $usertypeId);
         }
         $query->where('menus.parent_id',0);
         $query->orderBy('menus.order_by');
         $data=$query->get();
         foreach ($data as $key => $menu) {
            $subQuery = Menu::select('menus.id', 'menus.name', 'menus.url', 'menus.icon')
                ->join('menu_permissions as mp', 'mp.menu_id', '=', 'menus.id');
        
            // Optional: if request is made through 
            if ($request->has('user_type_id')) {
                $subQuery->where('mp.user_type_id', $usertypeId);
            }
        
            $subQuery->where('menus.parent_id', $menu->id)
                ->orderBy('menus.order_by');
        
            $submenus = $subQuery->get();
            
            // Assigning submenus to the current menu item
            $data[$key]->children = $submenus;
        }

 
         return $this->success(new MenuCollection($data));
     }
 
}
