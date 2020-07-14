<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\User;

use Symfony\Component\HttpFoundation\JsonResponse;

class GetUserExpenseTest extends UserTestBase
{
    public function testGetUserExpenses(): void
    {
        self::$user->request('GET', \sprintf('%s/%s/expenses.%s', $this->endpoint, self::IDS['user_id'], self::FORMAT));

        $response = self::$user->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertCount(1, $responseData['hydra:member']);
    }

    public function testGetAnotherUserExpenses(): void
    {
        self::$user->request('GET', \sprintf('%s/%s/expenses.%s', $this->endpoint, self::IDS['admin_id'], self::FORMAT));

        $response = self::$user->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertCount(0, $responseData['hydra:member']);
    }
}
