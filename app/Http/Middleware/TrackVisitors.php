<?php

namespace App\Http\Middleware;

use App\Helpers\UserSystemInfo;
use App\Models\Visitor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitors
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            DB::connection()->getPdo();
            if ($request->url() === url('/')) {
                $ipAddress = $request->ip();
                $userAgent = $request->userAgent();
                $key = "bouncing_time_{$ipAddress}_{$userAgent}";

                if (!Cache::has($key)) {
                    $bouncingTime = now();
                    Visitor::create([
                        'ip_address' => $ipAddress,
                        'browser_info' => UserSystemInfo::get_browsers(),
                        'os' => UserSystemInfo::get_os(),
                        'device' => UserSystemInfo::get_device(),
                        'user_agent' => $userAgent,
                    ]);
                    Cache::put($key, $bouncingTime, now()->addMinutes(20));
                }
            }
        } catch (\Exception $exception) {

        }
        return $next($request);

    }
}
