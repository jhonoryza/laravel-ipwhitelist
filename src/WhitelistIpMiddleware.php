<?php

namespace Jhonoryza\Ipwhitelist;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WhitelistIpMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $ipWhitelist = config('ipwhitelist.whitelist');
        $whitelist = array_map('trim', explode(',', $ipWhitelist));

        $clientIp = trim($request->ip());

        if (! in_array($clientIp, $whitelist)) {
            if ($request->wantsJson()) {
                Log::alert("Forbidden: Your IP: $clientIp is not allowed.");
                return response()->json([
                    'message' => 'Forbidden: Your IP is not allowed.',
                    'ip' => $clientIp
                ], 403);
            }

            return abort(403, "Forbidden: Your IP: $clientIp is not allowed.");
        }

        return $next($request);
    }
}
