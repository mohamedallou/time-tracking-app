<?php

declare(strict_types=1);

namespace App\Listener;

//TODO: translate the exception messages KernelEvents::EXCEPTION

use App\Exception\ProblemDetailsException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener(event: ExceptionEvent::class)]
class ExceptionListener
{
    public function __construct()
    {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ProblemDetailsException) {

        }
    }
}