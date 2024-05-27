<?php

namespace App\Service;

use App\Entity\LogEntry;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

class LogProcessor
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function processLogFile(string $filePath): void
    {
        $file = fopen($filePath, 'r');
        if ($file) {
            while (($line = fgets($file)) !== false) {
                $logEntry = $this->parseLogLine($line);
                if ($logEntry) {
                    $this->entityManager->persist($logEntry);
                }
            }
            $this->entityManager->flush();
            fclose($file);
        }
    }

    public function parseLogLine(string $line): ?LogEntry
    {
        $pattern = '/(\S+) - - \[(.+?)\] "POST \S+ HTTP\/1\.1" (\d+)/';
        if (preg_match($pattern, $line, $matches)) {
            $serviceName = $matches[1];
            $logDate = DateTime::createFromFormat('d/M/Y:H:i:s O', $matches[2]);
            $statusCode = (int) $matches[3];

            $logEntry = new LogEntry();
            $logEntry->setServiceName($serviceName);
            $logEntry->setLogDate($logDate);
            $logEntry->setStatusCode($statusCode);

            return $logEntry;
        }
        return null;
    }
}
