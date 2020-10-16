<?php

namespace App\Services\v1;

use App\Models\Photo;
use Illuminate\Support\Facades\Validator;

class PhotoService extends ResourceService
{
    protected $includes = ['author', 'album'];

    protected $queryFields = [
        'authorid' => 'author_id',
        'albumid' => 'album_id',
        'commentcount' => 'comment_count',
        'likecount' => 'like_count',
        'createdat' => 'created_at',
        'updatedat' => 'updated_at',
        'id' => 'id'
    ];

    protected $sortFields = [
        'authorid' => 'author_id',
        'albumid' => 'album_id',
        'commentcount' => 'comment_count',
        'likecount' => 'like_count',
        'id' => 'id'
    ];

    protected $columnMap = [
        'title' => 'title',
        'authorId' => 'author_id',
        'description' => 'description',
        'albumId' => 'album_id',
        'photo' => 'photo',
        'commentCount' => 'comment_count',
        'likeCount' => 'like_count',
        'isLikedByMe' => 'is_liked_by_me',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
        'id' => 'id'
    ];

    public function all($input)
    {
        $parms = $this->buildParameters($input);

        $query = Photo::offset($parms['offset'])->limit($parms['limit']);

        if (!empty($parms['sort'])) {
            $query = $query->orderBy($parms['sort'][0], $parms['sort'][1]);
        }

        if (!empty($parms['include'])) {
            $query->with($parms['include']);
        }

        if (!empty($parms['where'])) {
            $query->where($parms['where']);
        }

        return $query->get()->map(function ($photo) use ($parms) {
            return $this->formatToJson($photo, $parms['include']);
        });
    }

    public function patch($photo, $payload)
    {
        $this->validateSome($payload);

        return $this->update($photo, $payload);
    }

    public function put($photo, $payload)
    {
        $this->validateAll($payload);

        return $this->update($photo, $payload);
    }

    private function update($photo, $payload)
    {
        $actual = $this->convertToActual($payload);
        $photo->update($actual);

        return $this->formatToJson($photo);
    }

    private function validateAll($payload)
    {
        Validator::make($payload, [
            'title' => 'required|string|min:5|max:255',
            'authorId' => 'required|integer',
            'description' => 'required|min:60|max:1000',
            'photo' => 'present|nullable',
            'albumId' => 'required|integer',
            'commentCount' => 'present|integer|nullable',
            'likeCount' => 'present|integer|nullable',
            'isLikedByMe' => 'present|boolean|nullable',
        ])->validate();
    }

    private function validateSome($payload)
    {
        Validator::make($payload, [
            'title' => 'nullable|string|min:5|max:255',
            'authorId' => 'nullable|integer',
            'description' => 'nullable|min:60|max:1000',
            'photo' => 'unique:photos|file|nullable',
            'albumId' => 'nullable|integer',
            'commentCount' => 'integer|nullable',
            'likeCount' => 'integer|nullable',
            'isLikedByMe' => 'boolean|nullable',
        ])->validate();
    }

    public function formatToJson($photo, $includes = [])
    {
        $item = [
            'id' => $photo->id,
            'title' => $photo->title,
            'description' => $photo->description,
            'author' => [
                'id' => $photo->author_id,
            ],
            'album' => [
                'id' => $photo->album_id,
            ],
            'photo' => $photo->photo,
            'commentCount' => $photo->comment_count,
            'likeCount' => $photo->like_count,
            'isLikedByMe' => $photo->is_liked_by_me,
            'createdAt' => $photo->created_at,
            'updatedAt' => $photo->updated_at,
        ];

        if (in_array('author', $includes)) {
            $item['author'] = array_merge($item['author'], [
                'name' => $photo->author->name,
                'email' => $photo->author->email,
                'avatar' => $photo->author->avatar,
            ]);
        }

        if (in_array('album', $includes)) {
            $item['album'] = array_merge($item['album'], [
                'title' => $photo->album->title,
                'description' => $photo->album->description,
                'preview' => $photo->album->preview,
                'createdAt' => $photo->album->created_at,
                'updatedAt' => $photo->album->updated_at,
                'resourceUrl' => route('albums.show', $photo->album->id),
            ]);
        }

        return $item;
    }

    public function formatFromJson($data)
    {
        return [
            'title' => $data['title'],
            'description' => $data['description'],
            'author_id' => $data['authorId'],
            'album_id' => $data['albumId'],
            'photo' => $data['photo'],
            'comment_count' => $data['commentCount'] ?? 0,
            'like_count' => $data['likeCount'] ?? 0,
            'is_liked_by_me' => $data['isLikedByMe']
        ];
    }
}
