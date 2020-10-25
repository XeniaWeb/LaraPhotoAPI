<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\v1\UploadFilesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UploadFilesController extends Controller
{
    protected $service;

    function __construct(UploadFilesService $service)
    {
        $this->service = $service;
        $this->middleware('auth:api');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function uploadAvatar(Request $request)
    {
        return $this->service->uploadAvatar($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function uploadCover(Request $request)
    {
        return $this->service->uploadCover($request);
    }
}
