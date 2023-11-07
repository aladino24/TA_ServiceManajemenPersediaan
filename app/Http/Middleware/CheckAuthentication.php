<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected $serviceAuthenticationUrl;

    public function __construct()
    {
        $this->serviceAuthenticationUrl = Config::get('app.SERVICE_AUTHENTICATION_URL');
    }

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if($token && $this->checkAuthentication($token)){
            return $next($request);
        }
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    private function checkAuthentication($token){
        $response = Http::get($this->serviceAuthenticationUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return $response->status() === 200;
    }
}
