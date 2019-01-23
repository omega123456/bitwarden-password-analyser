<?php

namespace App\Models;

class LoginItem
{
    /**
     * @var string
     */
    private $siteName;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;

    /**
     * @var int
     */
    private $exploited;

    /**
     * @var int
     */
    private $numberOfDuplicates = 0;

    public function __construct(string $siteName, string $username, string $password)
    {
        $this->siteName = $siteName;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return int
     */
    public function getExploited(): int
    {
        return $this->exploited ?? 0;
    }

    /**
     * @param int $exploited
     */
    public function setExploited(int $exploited): void
    {
        $this->exploited = $exploited;
    }

    /**
     * @return string
     */
    public function getSiteName(): string
    {
        return $this->siteName;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function warningClass(): string
    {
        if ($this->getExploited() > 2 || $this->getNumberOfDuplicates() > 10) {
            return 'danger';
        }

        if ($this->getExploited() || $this->getNumberOfDuplicates()) {
            return 'warning';
        }

        return '';
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return int
     */
    public function getNumberOfDuplicates(): int
    {
        return $this->numberOfDuplicates;
    }

    /**
     * @param int $numberOfDuplicates
     */
    public function setNumberOfDuplicates(int $numberOfDuplicates): void
    {
        $this->numberOfDuplicates = $numberOfDuplicates;
    }

    public function increaseNumberOfduplicates()
    {
        $this->numberOfDuplicates++;
    }
}
