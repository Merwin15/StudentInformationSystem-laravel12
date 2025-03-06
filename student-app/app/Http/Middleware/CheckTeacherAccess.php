<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTeacherAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user() || !auth()->user()->isTeacher()) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to manage grades.');
        }

        return $next($request);
    }
} 