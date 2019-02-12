<?php

namespace App\Processors;

use Cookie;
use GuzzleHttp\Client;
use Illuminate\{
    Http\UploadedFile,
    Support\Collection};
use ZxcvbnPhp\Zxcvbn;

abstract class PasswordProcessor
{
    public const MEMCACHE_PREFIX = 'check_result_';
    /**
     * @var Zxcvbn
     */
    protected $passwordStrengthCheck;

    abstract public function isValidFile(UploadedFile $file): bool;

    abstract public function processPasswords(object $passwords): Collection;

    public function __construct(Zxcvbn $passwordStrengthCheck)
    {
        $this->passwordStrengthCheck = $passwordStrengthCheck;
    }

    public static function getClass(?string $passwordManagerType): string
    {
        switch ($passwordManagerType) {
            case 'lastpass':
                return LastPassProcessor::class;
            default:
                return BitwardenProcessor::class;
        }
    }

    public function clearCurrentFileData()
    {
        if (Cookie::get('file_hash')) {
            cache()->forget(self::MEMCACHE_PREFIX . Cookie::get('file_hash'));
        }

        Cookie::queue('file_hash', null, -1);
    }

    public function getProcessedResult(): Collection
    {
        if (!Cookie::get('file_hash')) {
            return collect([]);
        }

        return cache(self::MEMCACHE_PREFIX . Cookie::get('file_hash')) ?: collect([]);
    }

    protected function checkForExploits(Collection $loginItems)
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

    protected function checkForDuplicatePassword(Collection $loginItems)
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

    protected function checkPasswordStrength(Collection $loginItems)
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

    protected function removePasswords(Collection $loginItems)
    {
        /**
         * @var LoginItem $item
         */
        foreach ($loginItems as $item) {
            $item->setPassword('');
        }
    }
}
