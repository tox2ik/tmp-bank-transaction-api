<?php

use Symfony\Component\Dotenv\Dotenv;

// The check is to ensure we don't use .env in production
if (!isset($_SERVER['APP_ENV'])) {
    if (!class_exists(Dotenv::class)) {
        throw new \RuntimeException(
            'APP_ENV environment variable is not defined.'.
            'You need to define environment variables for configuration or '.
            'add "symfony/dotenv" as a Composer dependency to load variables from a .env file.'
        );
    }


    $dotEnvPath = getenv('DOT_ENV') ?: __DIR__.'/../../.env.test';
    (new Dotenv())->load($dotEnvPath);

}


#require __DIR__ . '/../../config/bootstrap.php';

