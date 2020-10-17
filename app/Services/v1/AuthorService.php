<?php

namespace App\Services\v1;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthorService extends ResourceService
{
    protected $includes = ['albums'];

    protected $queryFields = [
        'name' => 'name',
        'description' => 'description',
        'createdat' => 'created_at',
        'updatedat' => 'updated_at',
        'email' => 'email',
        'id' => 'id'
    ];

    protected $sortFields = [
        'name' => 'name',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
        'id' => 'id'
    ];

    protected $columnMap = [
        'name' => 'name',
        'avatar' => 'avatar',
        'description' => 'description',
        'cover' => 'cover',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
        'email' => 'email'
    ];

    public function all($input)
    {
        $parms = $this->buildParameters($input);

        $query = User::offset($parms['offset'])->limit($parms['limit']);

        if (!empty($parms['sort'])) {
            $query = $query->orderBy($parms['sort'][0], $parms['sort'][1]);
        }

        if (!empty($parms['include'])) {
            $query->with($parms['include']);
        }

        if (!empty($parms['where'])) {
            $query->where($parms['where']);
        }

        return $query->get()->map(function ($author) use ($parms) {
            return $this->formatToJson($author, $parms['include']);
        });
    }

    public function patch($author, $payload)
    {
        $this->validateSome($payload, $author);

        return $this->update($author, $payload);
    }

    public function put($author, $payload)
    {
        $this->validateAll($payload, $author);

        return $this->update($author, $payload);
    }

    private function update($author, $payload)
    {
        $actual = $this->convertToActual($payload);
        $author->update($actual);

        return $this->formatToJson($author);
    }

    private function validateAll($payload, $author)
    {
        Validator::make($payload, [
            'name' => 'required|string|min:5|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($author->id)],
            'description' => 'required|min:60|max:1000',
        ])->validate();
    }

    private function validateSome($payload, $author)
    {
        Validator::make($payload, [
            'name' => 'nullable|string|min:5|max:255',
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($author->id)],
            'description' => 'nullable|min:60|max:1000',
        ])->validate();
    }

    public function formatToJson($author, $includes = [])
    {
        $item = [
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

        if (in_array('albums', $includes)) {
            $item['photos'] = $author->albums->map(function ($album) {
                return [
                    'id' => $album->id,
                    'title' => $album->title,
                    'description' => $album->description,
                    'preview' => $album->preview,
                    'resourceUrl' => route('albums.show', $album->id),
                ];
            });
        }
        return $item;
    }

}
