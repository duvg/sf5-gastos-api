<?php

namespace App\Tests\Functional\Api\Group;

use Symfony\Component\HttpFoundation\JsonResponse;

class PutGroupTest extends GroupTestBase
{
    public function testPutGroup(): void
    {
        $payload = ['name' => 'new name'];

        self::$user->request(
            'PUT',
            \sprintf('%s/%s.%s', $this->endpoint, self::IDS['user_group_id'], self::FORMAT),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$user->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($payload['name'], $responseData['name']);
    }

    public function testPutAnotherGroup(): void
    {
        $payload = ['name' => 'new name'];

        self::$user->request(
            'PUT',
            \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_group_id'], self::FORMAT),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
