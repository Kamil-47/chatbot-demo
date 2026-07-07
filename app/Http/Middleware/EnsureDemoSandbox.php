<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class EnsureDemoSandbox
{
    private const COOKIE_NAME = 'demo_sid';
    private const COOKIE_LIFETIME_MINUTES = 1440;

    public function handle(Request $request, Closure $next): Response
    {
        if (!config('app.demo_mode')) {
            return $next($request);
        }

        $seedPath = config('app.demo_seed_db');

        if (!$seedPath || !file_exists($seedPath)) {
            return $next($request);
        }

        $rawSid = $request->cookies->get(self::COOKIE_NAME);
        $sid = $rawSid;
        $needsCookie = false;

        if (!$sid || !preg_match('/^[a-f0-9-]{36}$/', $sid)) {
            $sid = (string) Str::uuid();
            $needsCookie = true;
        }

        $sandboxPath = database_path("sandbox/{$sid}.db");

        if (!file_exists($sandboxPath)) {
            File::ensureDirectoryExists(database_path('sandbox'));
            $this->createSandbox($sandboxPath, $seedPath);
        } else {
            touch($sandboxPath);
        }

        config(['database.connections.sqlite.database' => $sandboxPath]);
        DB::purge('sqlite');

        $response = $next($request);

        if ($needsCookie) {
            $response->headers->setCookie(Cookie::create(
                name: self::COOKIE_NAME,
                value: $sid,
                expire: time() + self::COOKIE_LIFETIME_MINUTES * 60,
                path: '/',
                domain: null,
                secure: $request->secure(),
                httpOnly: true,
                raw: false,
                sameSite: Cookie::SAMESITE_LAX,
            ));
        }

        return $response;
    }

    private function createSandbox(string $sandboxPath, string $seedPath): void
    {
        $lockPath = $sandboxPath . '.lock';
        $lock = fopen($lockPath, 'c');

        try {
            flock($lock, LOCK_EX);

            if (!file_exists($sandboxPath)) {
                copy($seedPath, $sandboxPath);
                chmod($sandboxPath, 0664);
            }
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }
}
