<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if($token && $this->checkAuthentication($token)){
            // Ambil informasi pengguna dari respons check-token
            $userDetails = $this->getUserDetails($token);

            Session::put('user', $userDetails);
            return $next($request);
            // dd($userDetails);
        }
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    private function checkAuthentication($token){
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('http://127.0.0.1:8000/api/check-token');

        return $response->status() === 200;
    }

    private function getUserDetails($token)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('http://127.0.0.1:8000/api/check-token');

        return $response->json();
    }
}
