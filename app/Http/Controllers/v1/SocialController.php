<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Social;
use App\Models\User;
use App\Services\v1\SocialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $author = User::find(Auth::id());

        $socials = $request->input('social_id');
            $link = $request->input('link');
            $author->socials()->attach($socials, ['link' => $link]);
            return response(['message' => 'Социалки добавлены!','link' => $link, 'socials' => $socials,'Автор ' => $author], 201);
    }

    public function updateSocialInProfile(Request $request, Social $social, User $author)
    {

        $author = User::find(Auth::id());

//        $author->socials()->toggle($socials, ['link' => $link); // Или как там у вас называется поле.
        $author->socials()->syncWithoutDetaching([$request->id => ['link' => $request->input('link')]]);

        return response(['message' => 'Социалки обновлены!','Автор ' => $author], 200);
    }

    public function deleteSocialFromProfile(Request $request, Social $social, User $author)
    {
        $author = User::find(Auth::id());
        $author->socials()->detach($request->id);

        return response(['message' => 'Социалки удалены!','Автор ' => $author], 201);
    }
}
