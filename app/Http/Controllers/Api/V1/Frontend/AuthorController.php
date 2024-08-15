<?php

namespace App\Http\Controllers\Api\V1\Frontend;
use App\Http\Controllers\Api\V1\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;



use DB;
class AuthorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/frontend/authors",
     *     summary="Get a list of Authors",
     *     tags={"Authors"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search term for filtering by firstname or lastname or email or authorname",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *        @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page (optional, default: 20)",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/UserResource"))
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
        $query = User::query();
        $query->where('user_type_id', '2');

        if ($request->has('q')) {
            $searchTerm = strtoupper($request->input('q'));
            $query->where(function ($query) use ($searchTerm) {
                $query->where('firstname', 'ilike', '%' . $searchTerm . '%')
                ->orwhere('lastname', 'ilike', '%' . $searchTerm . '%')
                ->orwhere('email', 'ilike', '%' . $searchTerm . '%')
                ->orwhere('authorname', 'ilike', '%' . $searchTerm . '%');


            });
        }

        $authors = $query->paginate($perPage)->withPath($request->getPathInfo());
        return $this->success(new UserCollection($authors));
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
     * Display the specified resource.
     */
     /**
     * @OA\Get(
     *     path="/api/v1/frontend/authors/{slug}",
     *     summary="Get a specific author",
     *     tags={"Authors"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="The Name of the author to retrieve",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function show(string $name)
    {
        $author = User::where(DB::raw("CONCAT(firstname, ' ', lastname)"), 'like', '%' . $name . '%')->first();

        if ($author) {
            return $this->success(new UserResource($author));
        } else {
            return $this->error('User not found', Response::HTTP_NOT_FOUND);
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
