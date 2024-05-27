<?php

namespace App\Tests\Service;

use App\Entity\LogEntry;
use App\Service\LogProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use DateTime;

class LogProcessorTest extends TestCase
{
    public function testProcessLogFile(): void
    {
        $logContent = <<<LOG
USER-SERVICE - - [17/Aug/2018:09:21:53 +0000] "POST /users HTTP/1.1" 201
USER-SERVICE - - [17/Aug/2018:09:21:54 +0000] "POST /users HTTP/1.1" 400
INVOICE-SERVICE - - [17/Aug/2018:09:21:55 +0000] "POST /invoices HTTP/1.1" 201
LOG;

        $logFilePath = sys_get_temp_dir() . '/test_log.log';
        file_put_contents($logFilePath, $logContent);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(3))->method('persist');
        $entityManager->expects($this->once())->method('flush');

        $logProcessor = new LogProcessor($entityManager);
        $logProcessor->processLogFile($logFilePath);

        unlink($logFilePath);
    }

    public function testParseLogLine(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $logProcessor = new LogProcessor($entityManager);

        $line = 'USER-SERVICE - - [17/Aug/2018:09:21:53 +0000] "POST /users HTTP/1.1" 201';
        $logEntry = $logProcessor->parseLogLine($line);

        $this->assertInstanceOf(LogEntry::class, $logEntry);
        $this->assertEquals('USER-SERVICE', $logEntry->getServiceName());
        $this->assertEquals(201, $logEntry->getStatusCode());
        $this->assertEquals(new DateTime('2018-08-17 09:21:53'), $logEntry->getLogDate());
    }

    public function testParseLogLineWithInvalidLine(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $logProcessor = new LogProcessor($entityManager);

        $line = 'INVALID LOG LINE';
        $logEntry = $logProcessor->parseLogLine($line);

        $this->assertNull($logEntry);
    }
}
