## Bitwarden Password Analyser

Bitwarden password analyser is a tool to check your passwords for vulnerabilities. Currently it checks for the following:

 - if the password has been included in any known data breach (it uses the [haveibeenpwned api](https://haveibeenpwned.com/API/v2#PwnedPasswords))
 - the strength of the password
 - multiple usages of the same password

## Dependencies

 - `php: "^7.3"`

## Optional Dependencies

 - A queue managing system that laravel has a provider for eg. redis/sqs/mysql (you can skip this if you set
 `sync` as the queue provider. That way it will process the items right away instead of queueing. 
 The reason behind the queue is because it can take quite long to process the passwords especially if there are 
 more than 100 of them, so the web server might time out). Personally i would recommended redis,
 as its easy to set up, and really lightweight. https://tecadmin.net/install-redis-ubuntu/
 - web server to run the application (you can also use [homestead](https://laravel.com/docs/5.7/homestead), or just run `php artisan serve` directly from the 
 root folder of the application)

## Installation

Copy the `.env.example` file to `/.env` and set the `QUEUE_CONNECTION` variable to the queue provider of your choice.
After that you will need to run `composer install` first, then you can start up the queue consumer using the following command

    php artisan queue:work redis

Replace `redis` with the chosen queue provider. You can read more about how to use the laravel queues [here](https://laravel.com/docs/5.7/queues#running-the-queue-worker). 

## Usage

Once all set up, just open the url in the browser, and upload the password json file you exported from the bitwarden plugin.

https://help.bitwarden.com/article/export-your-data/ 
