<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    /**
     * Allowed MIME types for security.
     */
    protected array $allowedMimes = [
        'image/jpeg',
        'image/png',
        'image/jpg',
        'application/pdf',
    ];

    /**
     * Max file size in KB.
     */
    protected int $maxSizeKb = 2048; // 2MB

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:' . $this->maxSizeKb,
                'mimes:jpeg,png,jpg,pdf',
                function ($attribute, $value, $fail) {
                    // Double-check MIME type from file content, not extension
                    if ($value && !in_array($value->getMimeType(), $this->allowedMimes)) {
                        $fail('The file type is not allowed.');
                    }

                    // Check for PHP code in file content (backdoor prevention)
                    if ($value) {
                        $content = file_get_contents($value->getRealPath());
                        $dangerousPatterns = [
                            '<?php',
                            '<?=',
                            '<script',
                            'eval(',
                            'base64_decode(',
                            'shell_exec(',
                            'exec(',
                            'system(',
                            'passthru(',
                        ];

                        foreach ($dangerousPatterns as $pattern) {
                            if (stripos($content, $pattern) !== false) {
                                $fail('The file contains potentially malicious content.');
                                return;
                            }
                        }
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please select a file to upload.',
            'file.max' => 'File size must not exceed 2MB.',
            'file.mimes' => 'Only JPEG, PNG, and PDF files are allowed.',
        ];
    }
}
