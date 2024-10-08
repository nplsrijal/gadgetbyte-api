<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\AttributeOption;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\AttributeOptionCollection;
use App\Http\Resources\AttributeOptionResource;
use App\Http\Requests\StoreAttributeOptionRequest;
use App\Http\Requests\UpdateAttributeOptionRequest;
use App\Http\Requests\StoreFilterAttributeOptionRequest;

use DB;


class AttributeOptionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/attribute-options",
     *     summary="Get a list of AttributeOptions",
     *     tags={"AttributeOptions"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search term for filtering by name  ",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *        @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Search term for filtering by type  ",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *        @OA\Parameter(
     *         name="for",
     *         in="query",
     *         description="To list all options for dropdown (send:dropdown)  ",
     *         required=true,
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AttributeOptionResource"))
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

            $query = AttributeOption::query()
                ->select('attribute_options.*', 'attributes.name as attribute_name')
                ->join('attributes', 'attributes.id', '=', 'attribute_options.attribute_id');

            if ($request->has('q')) {
                $searchTerm = strtoupper($request->input('q'));
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('attribute_options.name', 'ilike', '%' . $searchTerm . '%')
                        ->orWhere('attributes.name', 'ilike', '%' . $searchTerm . '%');
                });
            }

            if($request->has('type'))
            {
                $query->where('type',$request->input('type'));
            }

            if($request->has('for') && $request->input('for')=='dropdown')
            {
                $data=$query->get();
            }
            else
            {
                $data = $query->paginate($perPage)->withPath($request->getPathInfo());

            }

            return $this->success(new AttributeOptionCollection($data));

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
     *     path="/api/v1/attribute-options",
     *     summary="Create a new AttributeOption",
     *     tags={"AttributeOptions"},
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
     *         description="AttributeOptions data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreAttributeOptionRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created menu",
     *         @OA\JsonContent(ref="#/components/schemas/AttributeOptionResource")
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
    public function store(StoreAttributeOptionRequest $request)
    {
        $validated = $request->validated();
        $userId = request()->header('X-User-Id');
        $validated['created_by'] = $userId;
        $validated['values'] = json_encode($validated['values']);
        
        $data = AttributeOption::create($validated);
       
        return $this->success(new AttributeOptionResource($data), 'AttributeOption created', Response::HTTP_CREATED);
   
    }

     /**
     * @OA\Get(
     *     path="/api/v1/attribute-options/{id}",
     *     summary="Get a specific menu",
     *     tags={"AttributeOptions"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the menu to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/AttributeOptionResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function show(string $id)
    {
        $data = AttributeOption::find($id);

        if ($data) {
            return $this->success(new AttributeOptionResource($data));
        } else {
            return $this->error('AttributeOption not found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttributeOption $menu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/v1/attribute-options/{id}",
     *     summary="Update an existing menu",
     *     tags={"AttributeOptions"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the menu to update",
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
     *         description="AttributeOption data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAttributeOptionRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated menu",
     *         @OA\JsonContent(ref="#/components/schemas/AttributeOptionResource")
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
    public function update(UpdateAttributeOptionRequest $request, string $id)
    {
        $data = AttributeOption::find($id);

        if (!$data) {
            return $this->error('AttributeOption not found', Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validated();
        $userId = request()->header('X-User-Id');
        $validatedData['updated_by'] = $userId;
        $data->update($validatedData);
    
        return $this->success(new AttributeOptionResource($data), 'AttributeOption updated', Response::HTTP_OK);
    }

   /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/attribute-options/{id}",
     *     summary="Delete an menu",
     *     tags={"AttributeOptions"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the menu to delete",
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
     *         description="Successfully deleted menu",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="AttributeOption deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/AttributeOptionResource")
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
        $data = AttributeOption::find($id);

        if (!$data) {
            return $this->error('AttributeOption not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new AttributeOptionResource($data), 'AttributeOption deleted successfully', Response::HTTP_OK);
    
    }

    /**
     * @OA\Post(
     *     path="/api/v1/category-attribute-options",
     *     summary="Create a new AttributeOption For Category Filter",
     *     tags={"AttributeOptions"},
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
     *         description="AttributeOptions data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreFilterAttributeOptionRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created menu",
     *         @OA\JsonContent(ref="#/components/schemas/AttributeOptionResource")
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
    public function storeFilterAttributes(StoreFilterAttributeOptionRequest $request)
    {
        $validated = $request->validated();
        $userId = request()->header('X-User-Id');
        $validated['created_by'] = $userId;
        $validated['values'] = json_encode($validated['values']);

        $attribute['created_by'] = $userId;
        $attribute['type']='category';
        $attribute['name']=ucwords(str_replace('-',' ',$validated['slug']));
        $attribute['slug']=$validated['slug'];
        $attribute['is_active']='Y';

          // Begin database transaction
          DB::beginTransaction();

          $check_attribute=Attribute::where('slug',$attribute['slug'])->first();
            if($check_attribute)
            {
                $attribute_id=$check_attribute->id;
            }
            else
            {
                $data = Attribute::create($attribute);
                $attribute_id=$data->id;

            }
        unset($validated['slug']);
        $validated['attribute_id']=$attribute_id;
        $data = AttributeOption::create($validated);

        // Check database transaction
        $transactionStatus = DB::transactionLevel();

        if ($transactionStatus > 0) {
            // Database transaction success
            DB::commit();
            return $this->success(new AttributeOptionResource($data), 'AttributeOption created', Response::HTTP_CREATED);
        } else {
            // Throw error
            throw new Exception('Could not save Data.', 1);
        }
       
   
    }
}
