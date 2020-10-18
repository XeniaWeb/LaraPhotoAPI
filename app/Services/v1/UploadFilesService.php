<?php

namespace App\Services\v1;

use Illuminate\Http\Request;

class UploadFilesService extends ResourceService
{
    public function uploadFile(Request $request)
    {
        return $request->file('avatar')->store('/', 'avatars');
    }

    public function formatToJson($author)
    {
        return [
            'id' => $author->id,
            'name' => $author->name,
            'email' => $author->email,
            'description' => $author->description,
            'avatar' => $author->avatar,
            'cover' => $author->cover,
            'createdAt' => $author->created_at,
            'updatedAt' => $author->updated_at,
            'resourceUrl' => route('authors.show', $author->id),
        ];
    }
}
