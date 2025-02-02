<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\InvalidTimeLogFound;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class InvalidTimeLogListener
{
    public function __invoke(InvalidTimeLogFound $event)
    {
        // TODO: Inform the manager of the log problem
    }
}