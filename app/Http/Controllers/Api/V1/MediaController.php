<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Resources\MediaCollection;
use App\Http\Resources\MediaResource;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use Illuminate\Support\Facades\Storage;



class MediaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/medias",
     *     summary="Get a list of Medias",
     *     tags={"Medias"},
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
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/MediaResource"))
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
        $query = Media::query();
        if ($request->has('q')) {
            $searchTerm = strtoupper($request->input('q'));
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'ilike', '%' . $searchTerm . '%');


            });
        }

        $data = $query->paginate($perPage)->withPath($request->getPathInfo());
        return $this->success(new MediaCollection($data));
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
     *     path="/api/v1/medias",
     *     summary="Create a new Media",
     *     tags={"Medias"},
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
     *         description="Medias data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreMediaRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created media",
     *         @OA\JsonContent(ref="#/components/schemas/MediaResource")
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
    public function store(StoreMediaRequest $request)
    {
        $validated = $request->validated();
        $userId = request()->header('X-User-Id');
        $imagesData = [];
        Storage::makeDirectory('public/uploads');
        Storage::makeDirectory('public/uploads/medias');

        foreach ($request->file('image') as $index => $image) {
            // $imageName = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
            // $image->storeAs('public/images', $imageName);

            $file= $request->file('image')[$index];
            $imageName= time() . '_' . $index . '.' . $image->getClientOriginalExtension();
            $file-> move(public_path('uploads/medias'), $imageName);
           

            $imagesData[] = [
                'name' => $request->name[$index],
                'image'=>'uploads/medias/'.$imageName,
                'caption' => $request->caption[$index],
                'description' => $request->description[$index],
                'created_by'=>$userId
            ];
        }
        
        $data = Media::insert($imagesData);
       
        return $this->success(new MediaResource($imagesData), 'Media created', Response::HTTP_CREATED);
   
    }

     /**
     * @OA\Get(
     *     path="/api/v1/medias/{id}",
     *     summary="Get a specific media",
     *     tags={"Medias"},
     *     security={{"bearer_token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the media to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/MediaResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     ),
     * )
     */
    public function show(string $id)
    {
        $data = Media::find($id)->join('users', 'medias.created_by', '=', 'users.id')->select('medias.*','users.firstname','users.lastname')->get();

        if ($data) {
            return $this->success(new MediaResource($data));
        } else {
            return $this->error('Media not found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Media $media)
    {
        //
    }

     /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/v1/medias/{id}",
     *     summary="Update an existing media",
     *     tags={"Menus"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the media to update",
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
     *         description="Menu data",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateMenuRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated media",
     *         @OA\JsonContent(ref="#/components/schemas/MenuResource")
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
    public function update(UpdateMediaRequest $request, string $id)
    {
        $data = Media::find($id);

        if (!$data) {
            return $this->error('Media not found', Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validated();
        $userId = request()->header('X-User-Id');
        $validatedData['updated_by'] = $userId;
        if($request->hasfile('image'))
        {
            $file = $request->file('image');
            $extenstion = $file->getClientOriginalExtension();
            $filename = time().'.'.$extenstion;
            $file-> move(public_path('uploads/medias'), $filename);
            $validatedData['image'] = $filename;
           
        }
        $data->update($validatedData);
    
        return $this->success(new MediaResource($data), 'Media updated', Response::HTTP_OK);
    }

   /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/medias/{id}",
     *     summary="Delete an media",
     *     tags={"Medias"},
     *     security={{"bearer_token": {}}, {"X-User-Id": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the media to delete",
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
     *         description="Successfully deleted media",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Media deleted successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/MediaResource")
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
        $data = Media::find($id);

        if (!$data) {
            return $this->error('Media not found', Response::HTTP_NOT_FOUND);
        }

        $userId = request()->header('X-User-Id');
        $data->update(['archived_by' => $userId]);
        $data->delete();
        return $this->success(new MediaResource($data), 'Media deleted successfully', Response::HTTP_OK);
    
    }
}
