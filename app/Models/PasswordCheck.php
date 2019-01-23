<?php

namespace App\Models;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use JsonSchema\Exception\JsonDecodingException;
use SplFileObject;

class PasswordCheck
{
    public function processPasswords(SplFileObject $file): array
    {
        $passwords = json_decode($file->fread($file->getSize()));

        if (!$passwords) {
            throw new JsonDecodingException();
        }

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
        $this->removePasswords($items);

        session(['processedPasswords' => $items]);

        return $passwords->items;
    }

    private function checkForExploits(Collection $loginItems)
    {
        $guzzle = new Client(['base_uri' => 'https://api.pwnedpasswords.com/']);

        /**
         * @var LoginItem $item
         */
        foreach ($loginItems as $item) {
            $passwordHash    = sha1($item->getPassword());
            $first5Character = substr($passwordHash, 0, 5);
            $lastCharacters  = substr($passwordHash, 5);

            $result = (string)$guzzle->get('range/' . $first5Character)->getBody();
            $result = explode("\r\n", $result);

            foreach ($result as $password) {
                if (stripos($password, $lastCharacters) === 0) {
                    $fragments = explode(':', strrev($password));

                    $item->setExploited((int)current($fragments));
                }
            }
        }
    }

    private function checkForDuplicatePassword(Collection $loginItems)
    {
        /**
         * @var LoginItem $item
         */
        foreach ($loginItems as $item) {
            /**
             * @var LoginItem $item2
             */
            foreach ($loginItems as $item2) {
                if ($item !== $item2 && $item->getPassword() == $item2->getPassword()) {
                    $item->increaseNumberOfduplicates();
                }
            }
        }
    }

    private function removePasswords(Collection $loginItems)
    {
        /**
         * @var LoginItem $item
         */
        foreach ($loginItems as $item) {
            $item->setPassword('');
        }
    }
}
