<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Expense;

use Symfony\Component\HttpFoundation\JsonResponse;

class DeleteExpenseTest extends ExpenseTestBase
{
    public function testDeleteExpense(): void
    {
        self::$user->request('DELETE', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['user_expense_id'], self::FORMAT));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteGroupExpenseId(): void
    {
        self::$user->request('DELETE', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['user_group_expense_id'], self::FORMAT));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteAnotherUserExpense(): void
    {
        self::$user->request('DELETE', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_expense_id'], self::FORMAT));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testDeleteAnotherGroupExpense(): void
    {
        self::$user->request('DELETE', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_group_expense_id'], self::FORMAT));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
