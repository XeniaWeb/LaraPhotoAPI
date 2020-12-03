<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Social;
use App\Models\User;
use App\Services\v1\SocialService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SocialController extends Controller
{
    protected $service;

    function __construct(SocialService $service)
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
        $socials = Social::all();

        return response(['socials' => $socials, 'message' => 'Retrieved successfully']);

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

            $social = $this->service->createNewSocial($request->input());

            return response(['social' => $social, 'message' => 'New social created successfully!'], 201);
        } catch (ValidationException $ve) {
            return response(['errors' => $ve->validator->errors(), 'message' => 'Validation Error'], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Social $social
     * @return \Illuminate\Http\Response
     */
    public function show(Social $social)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Social $social
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Social $social)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Social $social
     * @return \Illuminate\Http\Response
     */
    public function destroy(Social $social)
    {
        //
    }

    public function addSocialToProfile(Request $request, Social $social, User $author)
    {
        return response(['message' => 'Социалки добавлены!', 201]);
    }
}
