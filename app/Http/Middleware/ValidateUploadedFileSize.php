<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ValidateUploadedFileSize
{
    public function handle(Request $request, Closure $next): Response
    {
        $maximumKilobytes = (int) config('uploads.max_file_size_kb', 1024 * 1024);
        $templateMaximumKilobytes = (int) config('uploads.template_max_file_size_kb', 800 * 1024);
        $errors = [];

        $this->inspect($request->allFiles(), '', $maximumKilobytes, $templateMaximumKilobytes, $errors);

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $next($request);
    }

    private function inspect(
        array $files,
        string $prefix,
        int $maximumKilobytes,
        int $templateMaximumKilobytes,
        array &$errors
    ): void
    {
        foreach ($files as $key => $file) {
            $field = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if (is_array($file)) {
                $this->inspect($file, $field, $maximumKilobytes, $templateMaximumKilobytes, $errors);
                continue;
            }

            $isTemplateFile = str_contains(strtolower($field), 'template');
            $fieldMaximumKilobytes = $isTemplateFile ? $templateMaximumKilobytes : $maximumKilobytes;

            if ($file instanceof UploadedFile && $isTemplateFile) {
                $allowedExtensions = config('uploads.template_extensions', ['pdf', 'doc', 'docx']);
                $extension = strtolower($file->getClientOriginalExtension());

                if (! in_array($extension, $allowedExtensions, true)) {
                    $errors[$field][] = 'Format File Template harus PDF atau Word (.pdf, .doc, .docx).';
                }
            }

            if ($file instanceof UploadedFile && $file->getSize() > $fieldMaximumKilobytes * 1024) {
                $errors[$field][] = $isTemplateFile
                    ? 'Ukuran File Template maksimal 800 MB.'
                    : 'Ukuran setiap file maksimal 1 GB.';
            }
        }
    }
}
