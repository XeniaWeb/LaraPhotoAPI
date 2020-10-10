<?php

namespace App\Services\v1;

use App\Models\Photo;

class PhotoService
{
    public function all($limit, $offset, $sort)
    {
        // Set Max of $limit
        if ($limit > 50) {
            $limit = 50;
        }

        $sortInfo = $this->buildSort($sort);

//         ----thousands or 10s of thousands
//         return Photo::cursor()->filter(function ($photo) use ($offset) {
//            return $photo->id >= $offset;
//         })->take($limit)->map([$this, 'formatToJson']);

//        ---- without sort
//        return Photo::offset($offset)->limit($limit)->get()->map([$this, 'formatToJson']);

//        ---- with sort

        return Photo::orderBy($sortInfo[0], $sortInfo[1])->offset($offset)->limit($limit)->get()->map([$this, 'formatToJson']);
    }

    private function buildSort($rowSort)
    {
        if (empty($rowSort)) {
            return ['created_at', 'asc'];
        }

        $orderable = collect([
            'authorid' => 'author_id',
            'albumid' => 'album_id',
            'commentcount' => 'comment_count',
            'likecount' => 'like_count',
            'createdat' => 'created_at',
            'updatedat' => 'updated_at',
            'id' => 'id'
        ]);

        $direction = collect([
            'asc' => 'asc',
            'desc' => 'desc'
        ]);

        $parts = explode(':', strtolower($rowSort));
        $field = $orderable->get($parts[0]) ?? 'created_at';
        $dir = $direction->get($parts[1] ?? '') ?? 'asc';

        return [$field, $dir];
    }

    public function getAllFromJson()
    {
        return Photo::all()->map([$this, 'formatFromJson']);
    }

    public function formatToJson($photo)
    {
        return [
            'id' => $photo->id,
            'title' => $photo->title,
            'description' => $photo->description,
            'authorId' => $photo->author_id,
            'albumId' => $photo->album_id,
            'photo' => $photo->photo,
            'commentCount' => $photo->comment_count,
            'likeCount' => $photo->like_count,
            'isLikedByMe' => $photo->is_liked_by_me,
            'createdAt' => $photo->created_at,
            'updatedAt' => $photo->updated_at,
        ];
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
