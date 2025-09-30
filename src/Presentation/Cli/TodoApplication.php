<?php

namespace App\Presentation\Cli;

use Symfony\Component\Console\Application;

class TodoApplication extends Application
{
    public function __construct()
    {
        parent::__construct('TODO Manager', '1.0.0');
    }

    public function getLongVersion(): string
    {
        return sprintf(
            '<info>%s</info> version <comment>%s</comment>',
            $this->getName(),
            $this->getVersion()
        );
    }
}