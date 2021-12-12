<?php

namespace Modules\OutreachManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\OutreachManagement\Entities\Site;

class ApiController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['status' => false, 'message' => 'Unauthorized access'], 401);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json($this->guard()->user());
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['status' => false, 'message' => 'Logged out successfully!']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }

    public function sites(Request $request)
    {
        $sites = Site::paginate(15);
        foreach($sites as $site) {
            $data[] = [
                'website' => $site->website,
                'niche' => $site->niche,
                'domain_rating' => $site->domain_rating,
                'spam_score' => $site->spam_score,
                'traffic' => $site->traffic,
                'post_price' => $site->post_price,
                'link_price' => $site->link_price,
                'ahref_link' => $site->ahref_link,
                'ahref_snap' => $site->ahref_snap,
                'traffic_value' => $site->traffic_value
            ];
        }

        if(!isset($data))
        return response()->json(['status' => false, 'message' => 'No data found']);

        return response()->json(['status' => true, 'data' => $data]);
    }
}
