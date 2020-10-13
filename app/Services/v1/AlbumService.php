<?php


namespace App\Services\v1;

use App\Models\Album;

class AlbumService
{
    public function all($limit, $offset, $sort, $where, $include)
    {
        // Set Max of $limit
        if ($limit > 50) {
            $limit = 50;
        }

        $sortInfo = $this->buildSort($sort);
        $whereClauses = $this->buildWhere($where);
        $includes = $this->buildWith($include);

        $query = Album::orderBy($sortInfo[0], $sortInfo[1])->offset($offset)->limit($limit);

        if (!empty($includes)) {
            $query->with($includes);
        }

        if (!empty($whereClauses)) {
            $query->where($whereClauses);
        }

        return $query->get()->map(function ($album) use ($includes) {
            return $this->formatToJson($album, $includes);
        });
    }

    private function buildWith($rawInclude)
    {
        if (empty($rawInclude)) {
            return [];
        }

        $includeable = [
            'author',
            'photos'
        ];
        $parts = explode(',', strtolower($rawInclude));

        return array_intersect($includeable, $parts);

    }

    private function buildWhere($rawWhere)
    {
        if (empty($rawWhere)) {
            return [];
        }

        $queryable = collect([
            'author.id' => 'author_id',
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
            'author.id' => 'author_id',
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
        return Album::all()->map([$this, 'formatFromJson']);
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
