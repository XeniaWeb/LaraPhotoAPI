<?php


namespace App\Services\v1;

use App\Models\Album;

class AlbumService
{
    public function all($limit, $offset, $sort, $where)
    {
        // Set Max of $limit
        if ($limit > 50) {
            $limit = 50;
        }

        $sortInfo = $this->buildSort($sort);
        $whereClauses = $this->buildWhere($where);

        $query = Album::orderBy($sortInfo[0], $sortInfo[1])->offset($offset)->limit($limit);

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

    public function formatToJson($album)
    {
        return [
            'id' => $album->id,
            'title' => $album->title,
            'description' => $album->description,
            'authorId' => $album->author_id,
            'preview' => $album->preview,
            'createdAt' => $album->created_at,
            'updatedAt' => $album->updated_at,
        ];
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
