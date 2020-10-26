<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\v1\AuthorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthorController extends Controller
{
    protected $service;

    function __construct(AuthorService $service)
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
        $authors = $this->service->all($request->input());

        return response(['authors' => $authors, 'message' => 'Retrieved successfully']);
    }

    /**
     * Create a new author
     * is possible only through registration of a new user
     *
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param User $author
     * @return \Illuminate\Http\Response
     */
    public function show(User $author)
    {
        $author = $this->service->single($author, request()->input());

        return response(['author' => $author, 'message' => 'Retrieved successfully'], 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $author
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $author)
    {
        // POST -- full update with ?files
        // PUT -- replace; validate
        // PATCH -- partial update

        try {
            if ($request->isMethod('post')) {
                $author = User::query()->where('id', Auth::id())->first();
                if ($request->id == $author->id) {
                    $author = $this->service->postUpdate($request, $author);
                } else {
                    return response(['message' => 'You can only edit your own profile'], 422);
                }
            } elseif ($request->isMethod('patch')) {
                $author = $this->service->patch($author, $request->input());
            } else {
                $author = $this->service->put($author, $request->input());
            }

            return response(['author' => $author, 'message' => 'Updated successfully'], 201);
        } catch (ValidationException $ve) {
            return response(['errors' => $ve->validator->errors(), 'message' => 'Validation Error'], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
