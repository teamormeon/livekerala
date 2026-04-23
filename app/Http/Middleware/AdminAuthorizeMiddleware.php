<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthorizeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();
        if (!isset($user->role_id)){
            return $next($request);
        }
        $listItem = collect(config('role'))->pluck(['access'])->flatten();
        $filtered = $listItem->intersect($user->role->permission);

        if(!in_array($request->route()->getName(), $listItem->toArray()) ||  in_array($request->route()->getName(), $filtered->toArray()) ){
            return $next($request);
        }
        return  redirect()->route('admin.403');
    }
}
