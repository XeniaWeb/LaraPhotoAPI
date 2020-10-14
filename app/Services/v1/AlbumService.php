<?php

namespace App\Services\v1;

use App\Models\Album;

class AlbumService extends ResourceService
{
    protected array $includes = ['author', 'photos'];

    protected array $queryFields = [
        'author.id' => 'author_id',
        'createdat' => 'created_at',
        'updatedat' => 'updated_at',
        'id' => 'id'
    ];

    protected array $sortFields = [
        'title' => 'title',
        'author.id' => 'author_id',
        'createdat' => 'created_at',
        'updatedat' => 'updated_at',
        'id' => 'id'
    ];

    public function all($input)
    {
        $parms = $this->buildParameters($input);

        $query = Album::offset($parms['offset'])->limit($parms['limit']);

        if (!empty($parms['sort'])) {
            $query = $query->orderBy($parms['sort'][0], $parms['sort'][1]);
        }

        if (!empty($parms['include'])) {
            $query->with($parms['include']);
        }

        if (!empty($parms['where'])) {
            $query->where($parms['where']);
        }

        return $query->get()->map(function ($album) use ($parms) {
            return $this->formatToJson($album, $parms['include']);
        });
    }

    public function formatToJson($album, $includes = [])
    {
        $item = [
            'id' => $album->id,
            'title' => $album->title,
            'description' => $album->description,
            'author' => [
                'id' => $album->author_id,
            ],
            'preview' => $album->preview,
            'createdAt' => $album->created_at,
            'updatedAt' => $album->updated_at,
            'resourceUrl' => route('albums.show', $album->id),
        ];

        if (in_array('author', $includes)) {
            $item['author'] = array_merge($item['author'], [
                'name' => $album->author->name,
                'email' => $album->author->email,
                'avatar' => $album->author->avatar,
            ]);
        }

        if (in_array('photos', $includes)) {
            $item['photos'] = $album->photos->map(function ($card) {
                return [
                    'id' => $card->id,
                    'title' => $card->title,
                    'description' => $card->description,
                    'photo' => $card->photo,
                    'commentCount' => $card->comment_count,
                    'likeCount' => $card->like_count,
                    'isLikedByMe' => $card->is_liked_by_me,
                    'resourceUrl' => route('photos.show', $card->id),
                ];
            });
        }

        return $item;
    }

    public function formatFromJson($data)
    {
        return [
            'title' => $data['title'],
            'description' => $data['description'],
            'author_id' => $data['authorId'],
            'preview' => $data['preview'],
        ];
    }
}
