<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\TimeLogLogicException;
use App\Service\TimeTracker;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TimeTrackingController extends AbstractController
{
    public function __construct(
        private readonly TimeTracker $timeTracker,
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route(
        '/api/timetracking/start',
        name: 'timetracking_start',
        methods: ['POST'],
        format: 'json',
    )]
    public function start(): Response
    {
        $this->timeTracker->startTimeTracking();
        return $this->json(['success' => true],  Response::HTTP_CREATED);
    }

    #[Route(
        '/api/timetracking/stop',
        name: 'timetracking_stop',
        requirements: [
            '_format' => 'json',
        ],
        methods: ['POST'],
        format: 'json',
    )]
    public function end(): Response
    {
        $this->timeTracker->stopTimeTracking();
        return $this->json(['success' => true],  Response::HTTP_CREATED);
    }

    #[Route(
        '/api/timetracking/stop',
        name: 'timetracking_stop',
        requirements: [
            '_format' => 'json',
        ],
        methods: ['POST'],
        format: 'json',
    )]
    public function current(): Response
    {
        $currentTimeLog = $this->timeTracker->findCurrentActiveTimeLog();
        return $this->json(['success' => true, 'data' => $currentTimeLog],  Response::HTTP_CREATED);
    }
}