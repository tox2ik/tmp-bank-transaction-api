<?php

#use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Bundle\FrameworkBundle\Console\Application;


/**
 *
 * @property Application $app
 * @property Symfony\Bundle\FrameworkBundle\Kernel\ $kernel
 */
trait CreatesDatabaseTrait
{
    /**
     *
     */
    public function runMigrations()
    {
        $app = new Application($this->kernel);
        $app->setAutoExit(false);
        try {
            $bo = new \Symfony\Component\Console\Output\BufferedOutput();
            $app->run(new StringInput('doctrine:database:create'), $bo); // BUG: creates a file for sqlite-memory
            $app->run(new StringInput('doctrine:schema:update --force'), $bo);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            error_log($bo->fetch());
        }
        $this->app = $app;
    }

}
