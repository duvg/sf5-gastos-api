<?php

namespace App\Tests\Functional\Api\Category;

use Symfony\Component\HttpFoundation\JsonResponse;

class PostCategoryTest extends CategoryTestBase
{
    public function testCreateCategory(): void
    {
        $payload = [
            'name' => 'Category Admin test',
            'user' => \sprintf('/api/v1/users/%s', self::IDS['admin_id']),
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
        $this->assertEquals($payload['name'], $responseData['name']);
    }

    public function testCreateCategoryForAnotherUser(): void
    {
        $payload = [
            'name' => 'Category User test',
            'user' => \sprintf('/api/v1/users/%s', self::IDS['user_id']),
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
}
