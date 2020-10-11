<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\AlbumResource;
use App\Models\Album;
use Illuminate\Http\Request;
use App\Services\v1\AlbumService;
use Illuminate\Support\Facades\Validator;

class AlbumController extends Controller
{
    protected $service;

    function __construct(AlbumService $service)
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
        $limit = $request['limit'] ?? 6;
        $offset = $request['offset'] ?? 0;
        $sort = $request['sort'] ?? '';
        $where = $request['where'] ?? '';

        $albums = $this->service->all($limit, $offset, $sort, $where);

        return response(['albums'=> AlbumResource::collection($albums), 'message' => 'Retrieved successfully']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $this->service->formatFromJson($request->all());

            $validator = Validator::make($data, [
                'title' => 'required|max:255',
                'author_id' => 'required',
                'description' => 'required|min:60',
                'preview' => 'required',
            ]);

            if ($validator->fails()) {
                return response(['error' => $validator->errors(), 'message' => 'Validation Error'], 418);
            }

            $preview = $request->file('preview')->store('/', 'photos');

            if (!$preview) {
                return response(['message' => 'Error file upload'], 500);
            }

            $album =Album::create($data);
            $album->update(['preview' => $preview]);
            $album = $this->service->formatToJson($album);

            return response(['album' => new AlbumResource($album), 'message' => 'Created successfully'], 201);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function show(Album $album)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Album $album)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function destroy(Album $album)
    {
        $album->delete();
        return response(['message' => 'Deleted.'], 200);


    }
}
