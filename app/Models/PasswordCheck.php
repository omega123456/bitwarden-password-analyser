<?php

namespace App\Models;

use App\Jobs\ProcessPassword;
use Cookie;
use GuzzleHttp\Client;
use Illuminate\{
    Http\UploadedFile,
    Support\Collection};
use JsonSchema\Exception\JsonDecodingException;
use ZxcvbnPhp\Zxcvbn;

class PasswordCheck
{
    public const  MEMCACHE_PREFIX = 'check_result_';

    private const YEAR_IN_MINUTES = 525600;

    /**
     * @var Zxcvbn
     */
    private $passwordStrengthCheck;

    public function __construct(Zxcvbn $passwordStrengthCheck)
    {
        $this->passwordStrengthCheck = $passwordStrengthCheck;
    }

    public function queueRequest(UploadedFile $file)
    {
        $key = uniqid($file->getFilename(), true);

        if (Cookie::get('file_hash')) {
            cache()->forget(self::MEMCACHE_PREFIX . Cookie::get('file_hash'));
        }

        Cookie::queue('file_hash', null, -1);

        $file = $file->openFile();

        $passwords = json_decode($file->fread($file->getSize()));

        if (!$passwords) {
            throw new JsonDecodingException();
        }

        Cookie::queue('file_hash', $key, self::YEAR_IN_MINUTES);

        ProcessPassword::dispatch($passwords, $key);
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

    public function getProcessedResult(): Collection
    {
        if (!Cookie::get('file_hash')) {
            return collect([]);
        }

        return cache(self::MEMCACHE_PREFIX . Cookie::get('file_hash')) ?: collect([]);
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

            $body   = $guzzle->get('range/' . $first5Character)->getBody();
            $result = (string)$body;
            $result = explode("\r\n", $result);

            foreach ($result as $password) {
                if (stripos($password, $lastCharacters) === 0) {
                    $fragments = explode(':', strrev($password));

                    $item->setExploited((int)current($fragments));
                }
            }

            $body->close();
        }

        unset($guzzle, $body);
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

    private function checkPasswordStrength(Collection $loginItems)
    {
        /**
         * @var LoginItem $item
         */
        foreach ($loginItems as $item) {
            $strength = $this->passwordStrengthCheck->passwordStrength(
                $item->getPassword(),
                [$item->getUsername()]
            );

            $item->setPasswordStrength($strength['score']);
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
