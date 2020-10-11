<?php

namespace App\Services\v1;

use App\Models\Photo;

class PhotoService
{
    public function all($limit, $offset, $sort, $where)
    {
        // Set Max of $limit
        if ($limit > 50) {
            $limit = 50;
        }

        $sortInfo = $this->buildSort($sort);
        $whereClauses = $this->buildWhere($where);

//         ----thousands or 10s of thousands
//         return Photo::cursor()->filter(function ($photo) use ($offset) {
//            return $photo->id >= $offset;
//         })->take($limit)->map([$this, 'formatToJson']);

//        ---- without sort
//        return Photo::offset($offset)->limit($limit)->get()->map([$this, 'formatToJson']);

//        ---- with sort
        $query = Photo::orderBy($sortInfo[0], $sortInfo[1])->offset($offset)->limit($limit);

        if (!empty($whereClauses)) {
            $query->where($whereClauses);
        }

        return $query->get()->map([$this, 'formatToJson']);
    }

    private function buildWhere($rawWhere)
    {
        if (empty($rawWhere)) {
            return [];
        }

        $queryable = collect([
            'authorid' => 'author_id',
            'albumid' => 'album_id',
            'commentcount' => 'comment_count',
            'likecount' => 'like_count',
            'createdat' => 'created_at',
            'updatedat' => 'updated_at',
            'id' => 'id'
        ]);

        $operators = collect([
            'eq' => '=',
            'ne' => '<>',
            'lt' => '<',
            'lte' => '<=',
            'gt' => '>',
            'gte' => '>=',
        ]);

        //column:operator:value,column2:operator2:value2
        $rawClause = collect(explode(',', strtolower($rawWhere)));
        $clauses = collect([]);

        $rawClause->each(function ($item, $key) use ($queryable, $operators, $clauses) {
            //column:operator:value
            $parts = explode(':', $item);

            if (count($parts) != 3) {
                return false;
            }

            $field = $queryable->get($parts[0]) ?? '';
            $operator = $operators->get($parts[1]) ?? '';

            if (empty($field) || empty($operator)) {
                return false;
            }

            $clauses->push([
                $field,
                $operator,
                $parts[2]
            ]);
        });

        return $clauses->all();

    }

    private function buildSort($rawSort)
    {
        if (empty($rawSort)) {
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

        $parts = explode(':', strtolower($rawSort));
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
