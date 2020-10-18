<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Services\v1\CommentService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    protected $service;

    function __construct(CommentService $service)
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
        $comments = $this->service->all($request->input());

        return response(['comments' => $comments, 'message' => 'Retrieved successfully']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $comment = $this->service->createNewComment($request->input());

            return response(['comment' => $comment, 'message' => 'New comment created successfully!'], 201);
        } catch (ValidationException $ve) {
            return response(['errors' => $ve->validator->errors(), 'message' => 'Validation Error'], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        $comment = $this->service->single($comment, request()->input());

        return response(['comment' => $comment, 'message' => 'Retrieved successfully'], 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        // PUT -- replace; validate
        // PATCH -- partial update

        try {
            if ($request->isMethod('patch')) {
                $comment = $this->service->patch($comment, $request->input());
            } else {
                $comment = $this->service->put($comment, $request->input());
            }

            return response(['request' => $request->input(), 'comment' => $comment, 'message' => 'Updated successfully'], 201);
        } catch (ValidationException $ve) {
            return response(['errors' => $ve->validator->errors(), 'message' => 'Validation Error'], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response(['message' => 'Deleted.'], 200);
    }
}
