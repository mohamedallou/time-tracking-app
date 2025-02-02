<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\TimeLogStoreDto;
use App\Dto\TimeLogQueryDto;
use App\Exception\TimeLogDomainException;
use App\Service\TimeLogManager;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/api/admin', name: 'admin_')]
class TimeLogController extends AbstractController
{
    public function __construct(
        private readonly TimeLogManager $logManager,
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route(
        '/timelog',
        name: 'timelog_create',
        requirements: [
            '_format' => 'json',
        ],
        methods: ['POST'],
        format: 'json'
    )]
    #[IsGranted('ROLE_CREATE_TIME_LOG')]
    public function create(
        #[MapRequestPayload(validationGroups: ['create'])] TimeLogStoreDto $timeLogStoreDto
    ): Response {
        $timeLog = $this->logManager->createTimeLog($timeLogStoreDto);
        return $this->json(['success' => true, 'data' => $timeLog],  Response::HTTP_CREATED);
    }

    #[Route(
        '/timelog/{logId<\d+>}',
        name: 'timelog_edit',
        requirements: [
            '_format' => 'json',
        ],
        methods: ['PATCH'],
        format: 'json'
    )]
    public function update(
        #[MapRequestPayload(validationGroups: ['edit'])] TimeLogStoreDto $timeLogStoreDto,
        int $logId,
    ): Response {
        try {
            $timeLog = $this->logManager->updateTimeLog($timeLogStoreDto, $logId);
        } catch (TimeLogDomainException $exception) {
            return $this->handleTimeLogException($exception);
        }
        return $this->json(['success' => true, 'status' => Response::HTTP_OK, 'data' => $timeLog]);
    }

    #[Route(
        '/timelog/{id<\d+>}',
        name: 'timelog_delete',
        methods: ['DELETE'],
        format: 'json'
    )]
    public function delete(
        int $id
    ): Response {
        $this->logManager->deleteTimeLog($id);
        return $this->json(['success' => true, 'status' => Response::HTTP_OK]);
    }

    #[Route(
        '/timelog/{id<\d+>?}',
        name: 'timelog_read',
        methods: ['GET'],
        format: 'json'
    )]
    public function read(
        #[MapQueryString] TimeLogQueryDto $timeQueryDto = new TimeLogQueryDto(),
        ?int $id = null,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter] int $pageSize = 100,
    ): Response {
        if ($id === null) {
            $res = $this->logManager->findLogs($pageSize, $page, $timeQueryDto->getFromDate(), $timeQueryDto->getToDate());
            $total = $this->logManager->findLogsCount($timeQueryDto->getFromDate(), $timeQueryDto->getToDate());
            return $this->json(
                [
                    'status' => Response::HTTP_OK,
                    'success' => true,
                    'data' => $res,
                    'meta' => [
                        'total' => $total,
                        'page' => $page,
                        'pageSize' => $pageSize
                    ]
                ]
            );
        }
        $timeLog = $this->logManager->findOneTimeLog($id);

        return $this->json(['success' => true, 'status' => Response::HTTP_OK, 'data' => $timeLog]);
    }

    #[Route(
        '/timelog/statistics',
        name: 'timelog_statistics',
        methods: ['GET'],
        format: 'json'
    )]
    public function statistics(
        #[MapQueryString] TimeLogQueryDto $timeQueryDto = new TimeLogQueryDto(),
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter] int $pageSize = 1000,
    ): Response {
        $res = $this->logManager->findLogStatistics(
            $pageSize,
            $page,
            $timeQueryDto->getFromDate(),
            $timeQueryDto->getToDate()
        );
        $total = $this->logManager->findLogsCount($timeQueryDto->getFromDate(), $timeQueryDto->getToDate());
        return $this->json(
            [
                'status' => Response::HTTP_OK,
                'success' => true,
                'data' => $res,
                'meta' => [
                    'total' => $total,
                    'page' => $page,
                    'pageSize' => $pageSize
                ]
            ]
        );
    }

    #[Route(
        '/timelog/export/csv',
        name: 'timelog_export_csv',
        methods: ['GET'],
    )]
    public function exportCsv(
        #[MapQueryString] TimeLogQueryDto $timeQueryDto = new TimeLogQueryDto(),
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter] int $pageSize = 1000,
    ): Response {

        $res = $this->logManager->exportTimeLogsByInterval(
            $pageSize,
            $page,
            $timeQueryDto->getFromDate(),
            $timeQueryDto->getToDate()
        );

        $response = new Response(
            $res,
            Response::HTTP_OK,
            [
                'Content-Length' => strlen($res),
                'Content-type' => 'application/csv',
            ]
        );

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'timelogs-export.csv'
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * @param TimeLogDomainException|Exception $exception
     * @return JsonResponse
     */
    private function handleTimeLogException(TimeLogDomainException|Exception $exception): JsonResponse
    {
        if (!$exception->canUseMessageForUser()) {
            $this->logger->error('[TimeLogException] ' . $exception->getMessage(), ['exception' => $exception]);
            return $this->json(
                [
                    'title' => $this->translator->trans('BAD_REQUEST.title', domain: 'app'),
                    'details' => $this->translator->trans('BAD_REQUEST.DETAILS', domain: 'app'),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->json(
            [
                'title' => $this->translator->trans($exception->getTitle(), domain: 'app'),
                'details' => $this->translator->trans($exception->getDetails(), domain: 'app'),
            ],
            Response::HTTP_BAD_REQUEST
        );
    }
}