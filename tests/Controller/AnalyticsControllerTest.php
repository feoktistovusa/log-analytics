<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Mockery;

class AnalyticsControllerTest extends WebTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCountEndpointWithMultipleServiceNames(): void
    {
        $client = static::createClient();
        $client->request('GET', '/count', [
            'serviceNames' => 'USER-SERVICE,INVOICE-SERVICE',
            'statusCode' => 201,
            'startDate' => '2018-08-17T09:21:53',
            'endDate' => '2018-08-18T10:33:59',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('counter', $responseContent);
        $this->assertEquals(5, $responseContent['counter']);
    }

    public function testCountEndpointWithSingleServiceName(): void
    {
        $client = static::createClient();
        $client->request('GET', '/count', [
            'serviceNames' => 'USER-SERVICE',
            'statusCode' => 201,
            'startDate' => '2018-08-17T09:21:53',
            'endDate' => '2018-08-18T10:33:59',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('counter', $responseContent);
        $this->assertEquals(5, $responseContent['counter']);
    }

    public function testCountEndpointWithNoServiceNames(): void
    {
        $client = static::createClient();
        $client->request('GET', '/count', [
            'statusCode' => 201,
            'startDate' => '2018-08-17T09:21:53',
            'endDate' => '2018-08-18T10:33:59',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('counter', $responseContent);
        $this->assertEquals(5, $responseContent['counter']);
    }

    public function testCountEndpointWithDifferentStatusCode(): void
    {
        $client = static::createClient();
        $client->request('GET', '/count', [
            'serviceNames' => 'USER-SERVICE,INVOICE-SERVICE',
            'statusCode' => 400,
            'startDate' => '2018-08-17T09:21:53',
            'endDate' => '2018-08-18T10:33:59',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('counter', $responseContent);
        $this->assertEquals(5, $responseContent['counter']);
    }

    public function testCountEndpointWithNoFilters(): void
    {
        $client = static::createClient();
        $client->request('GET', '/count');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('counter', $responseContent);
        $this->assertEquals(5, $responseContent['counter']);
    }

    public function testCountEndpointWithInvalidDates(): void
    {
        $client = static::createClient();
        $client->request('GET', '/count', [
            'serviceNames' => 'USER-SERVICE,INVOICE-SERVICE',
            'statusCode' => 201,
            'startDate' => 'invalid-date',
            'endDate' => 'invalid-date',
        ]);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $responseContent);
        $this->assertEquals('Invalid startDate format', $responseContent['error']);
    }
}
