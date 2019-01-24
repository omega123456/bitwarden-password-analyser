<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZxcvbnPhp\Zxcvbn;

class Test extends Command
{
    public $signature = "test";

    public function handle( )
    {
        $a = new Zxcvbn();

        $start = microtime(true);
        for($i = 1; $i<= 100; $i++) {

            $a->passwordStrength(random_int(0, 999999));

        }

        echo microtime(true) - $start;
    }
}
