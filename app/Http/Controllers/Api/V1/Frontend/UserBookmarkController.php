<?php

namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Api\V1\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\UserBookmarkCollection;
use App\Http\Resources\UserBookmarkResource;
use App\Http\Requests\StoreUserBookmarkRequest;

use App\Models\UserBookmark;




class UserBookmarkController extends Controller
{
   /**
     * @OA\Get(
     *     path="/api/v1/frontend/bookmarks",
     *     summary="Get a list of Bookmarks",
     *     tags={"Bookmarks"},
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
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/UserBookmarkResource"))
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
        $userId = request()->header('X-User-Id');
        if(empty($perPage)){
            $perPage=20;
        }
        $query = UserBookmark::query();
        $query->where('user_id', '=',$userId );


        $data = $query->paginate($perPage)->withPath($request->getPathInfo());
        return $this->success(new UserBookmarkCollection($data));
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
     *     path="/api/v1/frontend/bookmarks",
     *     summary="Create a new UserBookmark",
     *     tags={"Bookmarks"},
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
     *         description="Bookmarks data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreUserBookmarkRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created bookmark",
     *         @OA\JsonContent(ref="#/components/schemas/UserBookmarkResource")
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
    public function store(StoreUserBookmarkRequest $request)
    {
        $validated = $request->validated();
        $userId = request()->header('X-User-Id');
        $validated['created_by'] = $userId;
        $validated['user_id'] = $userId;
        
        $data = UserBookmark::create($validated);
       
        return $this->success(new UserBookmarkResource($data), 'UserBookmark created', Response::HTTP_CREATED);
   
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
     *
     * @OA\Delete(
     *     path="/api/v1/frontend/bookmarks/{id}",
     *     summary="Delete an bookmark",
     *     tags={"Bookmarks"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the bookmark to delete",
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
     *         description="Successfully deleted bookmark",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="UserBookmark deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserBookmarkResource")
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
        $data = UserBookmark::find($id);

        if (!$data) {
            return $this->error('UserBookmark not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new UserBookmarkResource($data), 'UserBookmark deleted successfully', Response::HTTP_OK);
    
    }
}
