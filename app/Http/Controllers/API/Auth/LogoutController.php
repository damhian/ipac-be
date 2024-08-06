<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    use HttpResponses;

    public function __invoke(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success([], "Logout Success");
    }
}
