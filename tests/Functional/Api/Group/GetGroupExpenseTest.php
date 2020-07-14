<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Group;

use Symfony\Component\HttpFoundation\JsonResponse;

class GetGroupExpenseTest extends GroupTestBase
{
    public function testGetGroupExpenses(): void
    {
        self::$user->request('GET', \sprintf('%s/%s/expenses.%s', $this->endpoint, self::IDS['user_group_id'], self::FORMAT));

        $response = self::$user->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertCount(1, $responseData['hydra:member']);
    }

    public function testGetAnotherGroupExpenses(): void
    {
        self::$user->request('GET', \sprintf('%s/%s/expenses.%s', $this->endpoint, self::IDS['admin_group_id'], self::FORMAT));

        $response = self::$user->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertCount(0, $responseData['hydra:member']);
    }
}
