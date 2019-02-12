<?php

namespace App\Processors;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class LastPassProcessor extends PasswordProcessor
{
    public function isValidFile(UploadedFile $file): bool
    {
        // TODO: Implement isValidFile() method.
    }

    public function processPasswords(object $passwords): Collection
    {
        // TODO: Implement processPasswords() method.
    }
}
