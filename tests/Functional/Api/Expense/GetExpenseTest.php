<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Expense;

use Symfony\Component\HttpFoundation\JsonResponse;

class GetExpenseTest extends ExpenseTestBase
{
    public function testGetExpensesForAdmin(): void
    {
        self::$admin->request('GET', \sprintf('%s.%s', $this->endpoint, self::FORMAT));

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertCount(4, $responseData['hydra:member']);
    }

    public function testGetExpensesForUser(): void
    {
        self::$user->request('GET', \sprintf('%s.%s', $this->endpoint, self::FORMAT));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testGetUserExpenseAsAdmin(): void
    {
        self::$admin->request('GET', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['user_expense_id'], self::FORMAT));

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(self::IDS['user_expense_id'], $responseData['id']);
    }

    public function testGetAdminExpenseAsUser(): void
    {
        self::$user->request('GET', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_expense_id'], self::FORMAT));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
