<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PhotoResource;
use App\Models\Photo;
use App\Services\v1\PhotoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhotoController extends Controller
{
    protected $service;

    function __construct(PhotoService $service)
    {
        $this->service = $service;
        $this->middleware('auth:api', [
            'except' => ['index', 'show']
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $photos = $this->service->all($request->input());

        return response(['cards' => PhotoResource::collection($photos), 'message' => 'Retrieved successfully']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $this->service->formatFromJson($request->all());

            $validator = Validator::make($data, [
                'title' => 'required|max:255',
                'author_id' => 'required',
                'album_id' => 'required',
                'description' => 'required|min:60',
                'photo' => 'required|unique:photos',
                'is_liked_by_me' => 'required',
            ]);

            if ($validator->fails()) {
                return response(['error' => $validator->errors(), 'message' => 'Validation Error'], 418);
            }

            $card = $request->file('photo')->store('/', 'photos');

            if (!$card) {
                return response(['message' => 'Error file upload'], 500);
            }

            $photo = Photo::create($data);

            $photo->update(['photo' => $card]);
            $photo = $this->service->formatToJson($photo);

            return response(['card' => new PhotoResource($photo), 'message' => 'Created successfully'], 201);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Photo $photo
     * @return \Illuminate\Http\Response
     */
    public function show(Photo $photo)
    {
        $photo = $this->service->single($photo, request()->input());

        return response(['card' => new PhotoResource($photo), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Photo $photo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Photo $photo)
    {
        $photo->update($request->all());

        return response(['card' => new PhotoResource($photo), 'message' => 'Updated succesfully'], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Photo $photo
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Photo $photo)
    {
//   Этот код для локального компьютера
        if (file_exists(public_path('storage\\photos\\' . $photo->photo))) {
            unlink(public_path('storage\\photos\\' . $photo->photo));
            $photo->delete();
            return response(['message' => 'Deleted.'], 200);
        } else {
            return response(['message' => 'File not exist', 'file name' => $photo->photo], 404);
        }

//    Этот Код для хостинга

//        if (file_exists(env('LINK_IMG') . $photo->photo )) {
//            unlink(env('LINK_IMG') . $photo->photo );
//            $photo->delete();
//
//            return response(['message' => 'Deleted !'], 200);
//        } else {
//            return response(['message' => 'File not exist', 'file name' => env('LINK_IMG') . $photo->photo  ], 404);
//        }
    }
}
