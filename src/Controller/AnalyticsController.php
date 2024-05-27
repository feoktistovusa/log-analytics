<?php

namespace App\Controller;

use App\Entity\LogEntry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnalyticsController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/count', name: 'log_count', methods: ['GET'])]
    public function count(Request $request): Response
    {
        $serviceNames = $request->query->get('serviceNames');
        $statusCode = $request->query->get('statusCode');
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');

        $filters = [];

        if ($serviceNames) {
            $filters['serviceName'] = explode(',', $serviceNames);
        }

        if ($statusCode) {
            $filters['statusCode'] = (int) $statusCode;
        }

        if ($startDate) {
            $startDateObj = \DateTime::createFromFormat('Y-m-d\TH:i:s', $startDate);
            if ($startDateObj === false) {
                return $this->json(['error' => 'Invalid startDate format'], 400);
            }
            $filters['logDate']['gte'] = $startDateObj;
        }

        if ($endDate) {
            $endDateObj = \DateTime::createFromFormat('Y-m-d\TH:i:s', $endDate);
            if ($endDateObj === false) {
                return $this->json(['error' => 'Invalid endDate format'], 400);
            }
            $filters['logDate']['lte'] = $endDateObj;
        }

        $repository = $this->entityManager->getRepository(LogEntry::class);
        $count = $repository->countLogs($filters);

        return $this->json(['counter' => $count]);
    }
}
