<?php

namespace App\Jobs;

use App\Models\PasswordCheck;
use Carbon\Carbon;
use Illuminate\{
    Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels};

class ProcessPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * Create a new job instance.
     *
     * @param object $passwords
     * @param string $key
     */
    public function __construct(object $passwords, string $key)
    {
        $this->key       = $key;
        $this->passwords = $passwords;
    }

    /**
     * Execute the job.
     *
     * @param PasswordCheck $passwordCheck
     *
     * @return void
     * @throws \Exception
     */
    public function handle(PasswordCheck $passwordCheck)
    {
        cache(
            [PasswordCheck::MEMCACHE_PREFIX . $this->key => $passwordCheck->processPasswords($this->passwords)],
            Carbon::now()->addYear()
        );

        $this->passwords = null;
    }
}
