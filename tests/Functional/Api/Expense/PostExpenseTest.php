<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Expense;

use Symfony\Component\HttpFoundation\JsonResponse;

class PostExpenseTest extends ExpenseTestBase
{
    public function testCreateExpense(): void
    {
        $payload = [
            'category' => \sprintf('/api/v1/categories/%s', self::IDS['admin_category_id']),
            'user' => \sprintf('/api/v1/users/%s', self::IDS['admin_id']),
            'amount' => 500.50,
        ];

        self::$admin->request(
            'POST',
            \sprintf('%s.%s', $this->endpoint, self::FORMAT),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals($payload['amount'], $responseData['amount']);
        $this->assertEquals($payload['user'], $responseData['user']);
    }

    public function testCreateExpenseWithAnotherUserCategory(): void
    {
        $payload = [
            'category' => \sprintf('/api/v1/categories/%s', self::IDS['user_category_id']),
            'user' => \sprintf('/api/v1/users/%s', self::IDS['admin_id']),
            'amount' => 500.50,
        ];

        self::$admin->request(
            'POST',
            \sprintf('%s.%s', $this->endpoint, self::FORMAT),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$admin->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testCreateExpenseWithInvalidAmount(): void
    {
        $payload = [
            'category' => \sprintf('/api/v1/categories/%s', self::IDS['admin_category_id']),
            'user' => \sprintf('/api/v1/users/%s', self::IDS['admin_id']),
            'amount' => 'invalid_amount',
        ];

        self::$admin->request(
            'POST',
            \sprintf('%s.%s', $this->endpoint, self::FORMAT),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$admin->getResponse();

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testCreateExpenseForGroup(): void
    {
        $payload = [
            'category' => \sprintf('/api/v1/categories/%s', self::IDS['admin_group_category_id']),
            'user' => \sprintf('/api/v1/users/%s', self::IDS['admin_id']),
            'group' => \sprintf('/api/v1/groups/%s', self::IDS['admin_group_id']),
            'amount' => 500.50,
        ];

        self::$admin->request(
            'POST',
            \sprintf('%s.%s', $this->endpoint, self::FORMAT),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals($payload['amount'], $responseData['amount']);
        $this->assertEquals($payload['user'], $responseData['user']);
        $this->assertEquals($payload['group'], $responseData['group']);
    }

    public function testCreateExpenseForAnotherGroup(): void
    {
        $payload = [
            'category' => \sprintf('/api/v1/categories/%s', self::IDS['admin_group_category_id']),
            'user' => \sprintf('/api/v1/users/%s', self::IDS['admin_id']),
            'group' => \sprintf('/api/v1/groups/%s', self::IDS['user_group_id']),
            'amount' => 500.50,
        ];

        self::$admin->request(
            'POST',
            \sprintf('%s.%s', $this->endpoint, self::FORMAT),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$admin->getResponse();

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
