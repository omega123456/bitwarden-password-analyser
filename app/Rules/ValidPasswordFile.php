<?php

namespace App\Rules;

use App\Processors\PasswordProcessor;
use Illuminate\Contracts\Validation\Rule;

class ValidPasswordFile implements Rule
{
    /**
     * @var PasswordProcessor
     */
    private $passwordProcessor;

    /**
     * Create a new rule instance.
     *
     * @param PasswordProcessor $passwordProcessor
     */
    public function __construct(PasswordProcessor $passwordProcessor)
    {
        $this->passwordProcessor = $passwordProcessor;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return $this->passwordProcessor->isValidFile($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return '';
    }
}
