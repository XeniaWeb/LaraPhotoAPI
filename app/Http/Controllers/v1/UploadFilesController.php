<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\v1\UploadFilesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UploadFilesController extends Controller
{
    protected $service;

    function __construct(UploadFilesService $service)
    {
        $this->service = $service;
        $this->middleware('auth:api');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function uploadAvatar(Request $request)
    {
        if (!empty($request->file('avatar'))) {
            $avatar = $request->file('avatar')->store('/', 'avatars');

            $author_id = Auth::id();
            $author = User::query()->where('id', $author_id)->first();
            if (!empty($author['avatar'])) {
                $oldAvatar = $author['avatar'];
//   Этот код для локального компьютера

                if (file_exists(public_path('storage\\avatars\\' . $oldAvatar))) {
                    unlink(public_path('storage\\avatars\\' . $oldAvatar));
                }
//    Этот Код для хостинга

//        if (file_exists(env('LINK_IMG') . $oldAvatar )) {
//            unlink(env('LINK_IMG') . $oldAvatar );
//            }
            }
            $author->forceFill(['avatar' => $avatar])->save();
            $author = $this->service->formatToJson($author);

            return response(['avatar' => $avatar, 'author' => $author, 'message' => 'Avatar is uploaded successfully!'], 201);
        } else {
            return response(['message' => 'No files for uploading!'], 422);
        }
    }

    public function uploadCover(Request $request)
    {
        if (!empty($request->file('cover'))) {
            $cover = $request->file('cover')->store('/', 'photos');
            $author_id = Auth::id();
            $author = User::query()->where('id', $author_id)->first();
            if (!empty($author['cover'])) {
                $oldCover = $author['cover'];
//   Этот код для локального компьютера

                if (file_exists(public_path('storage\\photos\\' . $oldCover))) {
                    unlink(public_path('storage\\photos\\' . $oldCover));
                }
//    Этот Код для хостинга

//        if (file_exists(env('LINK_IMG') . $oldCover )) {
//            unlink(env('LINK_IMG') . $oldCover );
//            }
            }
            $author->forceFill(['cover' => $cover])->save();
            $author = $this->service->formatToJson($author);

            return response(['cover' => $cover, 'author' => $author, 'message' => 'Cover is uploaded successfully!'], 201);
        } else {
            return response(['message' => 'No files for uploading!'], 422);
        }
    }
}
