<?php

namespace App\Listeners;

use Illuminate\Log\Events\MessageLogged;
use Symfony\Component\Console\Output\ConsoleOutput;

class MessageLoggedListener
{
    public function handle(MessageLogged $event)
    {
        if (app()->runningInConsole()) {
            $output = new ConsoleOutput();
            $output->writeln("<error>{$event->message}</error>");
        }
    }
}
