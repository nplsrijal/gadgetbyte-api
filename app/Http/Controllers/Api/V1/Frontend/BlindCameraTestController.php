<?php

namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Api\V1\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\BlindCameraTest;


use App\Http\Resources\BlindCameraTestCollection;
use App\Http\Resources\BlindCameraTestResource;


class BlindCameraTestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/frontend/blind-camera-test",
     *     summary="Get a list of BlindCameraTests",
     *     tags={"Frontend BlindCameraTests"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search term for filtering by name  ",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="is_highlighted",
     *         in="query",
     *         description="To show highlighted specific (Y/N)",
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/BlindCameraTestResource"))
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
        $query = BlindCameraTest::query();
        if ($request->has('q')) {
            $searchTerm = strtoupper($request->input('q'));
            $query->where(function ($query) use ($searchTerm) {
                $query->where('product_a_title', 'ilike', '%' . $searchTerm . '%')
                ->orwhere('product_b_title', 'ilike', '%' . $searchTerm . '%');

            });
        }
        if ($request->has('is_highlighted')) {
            $searchTerm = strtoupper($request->input('is_highlighted'));
            $query->where('is_highlighted',$searchTerm);

        }

        $data = $query->paginate($perPage)->withPath($request->getPathInfo());
        return $this->success(new BlindCameraTestCollection($data));
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
     */
    public function destroy(string $id)
    {
        //
    }
}
