<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UniqueVisitorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // セッションに一意のIDが存在しない場合、新たに生成
        if (!$request->session()->has('player_id')) {
            $playerId = uniqid('player_', true); // 一意のIDを生成
            $request->session()->put('player_id', $playerId); // セッションに保存
        }

        return $next($request);
    }
}
