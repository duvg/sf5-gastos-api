<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Expense;

use Symfony\Component\HttpFoundation\JsonResponse;

class PutExpenseTest extends ExpenseTestBase
{
    public function testPutExpense(): void
    {
        $payload = [
            'category' => \sprintf('/api/v1/categories/%s', self::IDS['admin_category_id']),
            'amount' => 1000,
        ];

        self::$admin->request(
            'PUT',
            \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_expense_id'], self::FORMAT),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($payload['amount'], $responseData['amount']);
    }

    public function testPutExpenseWithAnotherUserCategory(): void
    {
        $payload = [
            'category' => \sprintf('/api/v1/categories/%s', self::IDS['user_category_id']),
            'amount' => 1000,
        ];

        self::$admin->request(
            'PUT',
            \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_expense_id'], self::FORMAT),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$admin->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testPutGroupExpense(): void
    {
        $payload = [
            'category' => \sprintf('/api/v1/categories/%s', self::IDS['admin_group_category_id']),
            'amount' => 4000,
        ];

        self::$admin->request(
            'PUT',
            \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_group_expense_id'], self::FORMAT),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($payload['amount'], $responseData['amount']);
        $this->assertEquals($payload['category'], $responseData['category']);
    }

    public function testPutAnotherUserGroupExpense(): void
    {
        $payload = [
            'category' => \sprintf('/api/v1/categories/%s', self::IDS['admin_group_category_id']),
            'amount' => 4000,
        ];

        self::$user->request(
            'PUT',
            \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_group_expense_id'], self::FORMAT),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
