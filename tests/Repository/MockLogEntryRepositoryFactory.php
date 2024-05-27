<?php

namespace App\Tests\Repository;

use App\Repository\LogEntryRepository;
use Doctrine\Persistence\ObjectRepository;
use Mockery;

class MockLogEntryRepositoryFactory
{
    public static function create(): ObjectRepository
    {
        $mock = Mockery::mock(LogEntryRepository::class);
        $mock->shouldReceive('countLogs')
            ->andReturn(5);

        return $mock;
    }
}
