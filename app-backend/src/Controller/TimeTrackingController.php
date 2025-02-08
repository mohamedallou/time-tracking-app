<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\TimeTracker;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('IS_AUTHENTICATED')]
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
    public function start(#[CurrentUser] ?User $user): Response
    {
        $this->timeTracker->startTimeTracking($user);
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
    public function end(#[CurrentUser] ?User $user): Response
    {
        $this->timeTracker->stopTimeTracking($user);
        return $this->json(['success' => true],  Response::HTTP_CREATED);
    }

    #[Route(
        '/api/timetracking/current',
        name: 'timetracking_current',
        requirements: [
            '_format' => 'json',
        ],
        methods: ['GET'],
        format: 'json',

    )]
    public function current(#[CurrentUser]?User $user): Response
    {
        $currentTimeLog = $this->timeTracker->findCurrentActiveTimeLogForUser($user);
        return $this->json(
            ['success' => true, 'data' => $currentTimeLog],
            Response::HTTP_CREATED,
            context: ['groups' => 'timelog_details']
        );
    }
}