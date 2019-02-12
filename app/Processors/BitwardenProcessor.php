<?php

namespace App\Processors;

use App\Models\LoginItem;
use Illuminate\{
    Http\UploadedFile,
    Support\Collection};

class BitwardenProcessor extends PasswordProcessor
{
    public function isValidFile(UploadedFile $file): bool
    {
        $file = $file->openFile();

        $passwords = json_decode($file->fread($file->getSize()));

        return !empty($passwords);
    }

    public function processPasswords(object $passwords): Collection
    {
        $items = new Collection();

        foreach ($passwords->items as $item) {
            if (!isset($item->login)) {
                continue;
            }

            $items->push(
                new LoginItem(
                    $item->name,
                    (string)$item->login->username,
                    (string)$item->login->password
                )
            );
        }

        $this->checkForExploits($items);
        $this->checkForDuplicatePassword($items);
        $this->checkPasswordStrength($items);
        $this->removePasswords($items);

        return $items;
    }
}
