<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Fields that should NOT be sanitized (like passwords, rich text).
     */
    protected array $except = [
        'password',
        'password_confirmation',
        'current_password',
    ];

    /**
     * Sanitize incoming request data to prevent XSS attacks.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        array_walk_recursive($input, function (&$value, $key) {
            if (!is_string($value) || in_array($key, $this->except)) {
                return;
            }

            // Trim whitespace
            $value = trim($value);

            // Strip HTML tags
            $value = strip_tags($value);

            // Convert special characters to HTML entities
            $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
        });

        $request->merge($input);

        return $next($request);
    }
}
