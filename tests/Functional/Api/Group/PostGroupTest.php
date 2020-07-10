<?php

namespace App\Tests\Functional\Api\Group;

use Symfony\Component\HttpFoundation\JsonResponse;

class PostGroupTest extends GroupTestBase
{
    public function testCreateGroup(): void
    {
        $payload = [
            'name' => 'Admin\'s Group 2',
            'owner' => \sprintf('/api/v1/users/%s', self::IDS['admin_id']),
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

    public function testCreateGroupForAnotherUSer(): void
    {
        $payload = [
            'name' => 'Admin\'s Group 2',
            'owner' => \sprintf('/api/v1/users/%s', self::IDS['user_id']),
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
