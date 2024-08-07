<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageAccessControl
{
    public function handle(Request $request, Closure $next)
    {
        // Get the current user
        $user = Auth::user();

        // Get the page slug from the URL
        $pageSlug = $request->segment(2) ?: $request->segment(1);

        // If 'admin' is in the URL, allow access
        if ($request->segment(1) === 'admin') {
            return $next($request);
        }

        // Check if the user is admin (e.g., using a 'is_admin' flag on the User model)
        if ($user->role==='admin') {
            return $next($request);
        }

        // Retrieve allowed page slugs from the database or configuration
        $allowedPageSlugs = $user->allowedPageSlugs(); // Assuming a method that returns an array of allowed slugs

        // Check if the page slug is in the list of allowed slugs
        if (in_array($pageSlug, $allowedPageSlugs)) {
            return $next($request);
        }

        // If the user does not have permission, return a 403 Forbidden response
        return ApiResponseHelper::sendErrorResponse([], 'Forbidden', 403);

    }
}
