<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\v1\AuthorService;
use Illuminate\Http\Request;
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
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
        // PUT -- replace; validate
        // PATCH -- partial update

        try {
            if ($request->isMethod('patch')) {
                $author = $this->service->patch($author, $request->input());
            } else {
                $author = $this->service->put($author, $request->input());
            }

            return response(['запрос' => $request->input(), 'author' => $author, 'message' => 'Updated succesfully'], 201);
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
