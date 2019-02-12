<?php

namespace App\Jobs;

use App\Processors\PasswordProcessor;
use Carbon\Carbon;
use Cookie;
use Exception;
use Illuminate\{
    Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Http\UploadedFile,
    Queue\InteractsWithQueue,
    Queue\SerializesModels};

class PasswordQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const YEAR_IN_MINUTES = 525600;

    private const FAILED_JOBS_MEMCACHE_KEY = '___failed_jobs___';

    public $tries = 2;

    public $timeout = Carbon::SECONDS_PER_MINUTE * 5;

    /**
     * @var string
     */
    private $key;
    /**
     * @var object
     */
    private $passwords;
    /**
     * @var string
     */
    private $passwordProcessorClass;

    public function __construct(UploadedFile $file, ?string $passwordManagerType)
    {
        $file = $file->openFile();

        $passwords = json_decode($file->fread($file->getSize()));

        $key = uniqid($file->getFilename(), true);

        Cookie::queue('file_hash', $key, self::YEAR_IN_MINUTES);

        $this->key                    = $key;
        $this->passwords              = $passwords;
        $this->passwordProcessorClass = PasswordProcessor::getClass($passwordManagerType);
    }

    public static function getFailedJobs(): array
    {
        return cache(static::FAILED_JOBS_MEMCACHE_KEY) ?: [];
    }

    public static function hasJobFailed(string $key): bool
    {
        return in_array($key, static::getFailedJobs());
    }

    public function handle()
    {
        $passwordProcessor = app($this->passwordProcessorClass);

        cache(
            [
                PasswordProcessor::MEMCACHE_PREFIX . $this->key =>
                    $passwordProcessor->processPasswords($this->passwords)
            ],
            Carbon::now()->addYear()
        );

        $this->passwords = null;
    }

    public function failed(Exception $exception)
    {
        $failedJobs = static::getFailedJobs();

        $failedJobs[] = $this->key;

        cache(
            [static::FAILED_JOBS_MEMCACHE_KEY => $failedJobs],
            Carbon::now()->addDay()
        );

        throw $exception;
    }
}
