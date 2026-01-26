<?php

namespace App\Services;

use App\Models\ErrorLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

class ErrorLogService
{
    /**
     * Sensitive fields that should be masked in request data.
     */
    protected static array $sensitiveFields = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'token',
        'api_key',
        'secret',
        'credit_card',
        'cvv',
    ];

    /**
     * Exception to type mapping.
     */
    protected static array $exceptionTypeMap = [
        // Validation errors
        \Illuminate\Validation\ValidationException::class => 'validation',
        
        // Authentication errors
        \Illuminate\Auth\AuthenticationException::class => 'auth',
        \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException::class => 'auth',
        
        // Authorization errors
        \Illuminate\Auth\Access\AuthorizationException::class => 'authorization',
        \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException::class => 'authorization',
        
        // Database errors
        \Illuminate\Database\QueryException::class => 'database',
        \PDOException::class => 'database',
        
        // Not found errors
        \Illuminate\Database\Eloquent\ModelNotFoundException::class => 'database',
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class => 'server',
        
        // File/Upload errors
        \Illuminate\Http\Exceptions\PostTooLargeException::class => 'file',
        \Symfony\Component\HttpFoundation\File\Exception\FileException::class => 'file',
        
        // HTTP errors
        \Symfony\Component\HttpKernel\Exception\HttpException::class => 'server',
        \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class => 'server',
        
        // Network/External API errors
        \GuzzleHttp\Exception\RequestException::class => 'api',
        \Illuminate\Http\Client\RequestException::class => 'api',
    ];

    /**
     * Human-readable message templates based on exception type.
     */
    protected static array $messageTemplates = [
        'validation' => [
            'title' => 'Data Tidak Valid',
            'message' => 'Data yang dimasukkan tidak sesuai dengan format yang diharapkan.',
        ],
        'auth' => [
            'title' => 'Autentikasi Gagal',
            'message' => 'Sesi login telah berakhir atau kredensial tidak valid. Silakan login kembali.',
        ],
        'authorization' => [
            'title' => 'Akses Ditolak',
            'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.',
        ],
        'database' => [
            'title' => 'Kesalahan Database',
            'message' => 'Terjadi masalah saat memproses data. Tim teknis telah diberitahu.',
        ],
        'server' => [
            'title' => 'Kesalahan Server',
            'message' => 'Terjadi kesalahan pada server. Silakan coba lagi atau hubungi administrator.',
        ],
        'file' => [
            'title' => 'Kesalahan File',
            'message' => 'Gagal memproses file. Pastikan file yang diunggah sesuai format dan ukuran.',
        ],
        'api' => [
            'title' => 'Layanan External Gagal',
            'message' => 'Gagal berkomunikasi dengan layanan eksternal. Silakan coba lagi nanti.',
        ],
        'network' => [
            'title' => 'Koneksi Terputus',
            'message' => 'Terjadi masalah koneksi jaringan. Periksa koneksi internet Anda.',
        ],
        'unknown' => [
            'title' => 'Kesalahan Tidak Terduga',
            'message' => 'Terjadi kesalahan yang tidak terduga. Tim teknis telah diberitahu.',
        ],
    ];

    /**
     * Specific exception message translations.
     */
    protected static array $specificTranslations = [
        // Database
        'SQLSTATE[23000]' => 'Data sudah ada atau terjadi konflik dengan data lain.',
        'SQLSTATE[22001]' => 'Data yang dimasukkan terlalu panjang.',
        'SQLSTATE[23503]' => 'Data tidak dapat dihapus karena masih digunakan oleh data lain.',
        'SQLSTATE[42S02]' => 'Tabel database tidak ditemukan.',
        'Duplicate entry' => 'Data dengan nilai yang sama sudah ada dalam sistem.',
        'foreign key constraint' => 'Data terkait dengan data lain dan tidak dapat diproses.',
        
        // File
        'The file failed to upload' => 'Gagal mengunggah file. Silakan coba lagi.',
        'exceeds your upload_max_filesize' => 'Ukuran file melebihi batas maksimum yang diizinkan.',
        'The file is too large' => 'File terlalu besar untuk diunggah.',
        
        // Auth
        'Unauthenticated' => 'Sesi Anda telah berakhir. Silakan login kembali.',
        'Token has expired' => 'Sesi login telah kedaluwarsa.',
        'Token is invalid' => 'Token tidak valid.',
        
        // Validation
        'The given data was invalid' => 'Data yang dimasukkan tidak valid.',
    ];

    /**
     * Log an exception with human-readable translation.
     */
    public static function log(Throwable $exception, ?string $customTitle = null, ?string $customMessage = null): ErrorLog
    {
        $user = Auth::user();
        $type = self::determineType($exception);
        $severity = self::determineSeverity($exception);
        $translated = self::translateException($exception, $type);

        return ErrorLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'Guest',
            'type' => $type,
            'severity' => $severity,
            'title' => $customTitle ?? $translated['title'],
            'message' => $customMessage ?? $translated['message'],
            'exception_class' => get_class($exception),
            'exception_message' => $exception->getMessage(),
            'file' => self::shortenPath($exception->getFile()),
            'line' => $exception->getLine(),
            'trace' => self::formatTrace($exception),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'request_data' => self::sanitizeRequestData(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Determine error type from exception.
     */
    protected static function determineType(Throwable $exception): string
    {
        $exceptionClass = get_class($exception);

        // Check exact match first
        if (isset(self::$exceptionTypeMap[$exceptionClass])) {
            return self::$exceptionTypeMap[$exceptionClass];
        }

        // Check parent classes
        foreach (self::$exceptionTypeMap as $class => $type) {
            if ($exception instanceof $class) {
                return $type;
            }
        }

        return 'unknown';
    }

    /**
     * Determine severity based on exception.
     */
    protected static function determineSeverity(Throwable $exception): string
    {
        // Validation and auth issues are warnings
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return 'warning';
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return 'warning';
        }

        // Database and server errors are errors
        if ($exception instanceof \Illuminate\Database\QueryException) {
            return 'error';
        }

        // Critical for PDO errors, out of memory, etc.
        if ($exception instanceof \PDOException || 
            $exception instanceof \ErrorException ||
            str_contains($exception->getMessage(), 'memory')) {
            return 'critical';
        }

        return 'error';
    }

    /**
     * Translate exception to human-readable message.
     */
    protected static function translateException(Throwable $exception, string $type): array
    {
        $originalMessage = $exception->getMessage();

        // Check specific translations first
        foreach (self::$specificTranslations as $pattern => $translation) {
            if (str_contains($originalMessage, $pattern)) {
                return [
                    'title' => self::$messageTemplates[$type]['title'] ?? 'Error',
                    'message' => $translation,
                ];
            }
        }

        // Handle validation exceptions specially
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $errors = $exception->errors();
            $firstError = collect($errors)->flatten()->first();
            return [
                'title' => 'Validasi Gagal',
                'message' => $firstError ?? 'Data tidak valid.',
            ];
        }

        // Return template message for type
        return self::$messageTemplates[$type] ?? self::$messageTemplates['unknown'];
    }

    /**
     * Format stack trace (limit to first 5 frames).
     */
    protected static function formatTrace(Throwable $exception): array
    {
        $trace = $exception->getTrace();
        $formatted = [];

        foreach (array_slice($trace, 0, 5) as $frame) {
            $formatted[] = [
                'file' => self::shortenPath($frame['file'] ?? ''),
                'line' => $frame['line'] ?? 0,
                'function' => $frame['function'] ?? '',
                'class' => $frame['class'] ?? '',
            ];
        }

        return $formatted;
    }

    /**
     * Shorten file path for readability.
     */
    protected static function shortenPath(?string $path): string
    {
        if (!$path) return '';
        
        // Remove base path
        $basePath = base_path();
        return str_replace($basePath . DIRECTORY_SEPARATOR, '', $path);
    }

    /**
     * Sanitize request data (remove sensitive fields).
     */
    protected static function sanitizeRequestData(): array
    {
        $data = Request::except(self::$sensitiveFields);
        
        // Recursively mask sensitive values
        array_walk_recursive($data, function (&$value, $key) {
            if (in_array(strtolower($key), self::$sensitiveFields)) {
                $value = '***MASKED***';
            }
        });

        // Limit size
        return array_slice($data, 0, 20);
    }
}
