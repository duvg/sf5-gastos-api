<?php

namespace App\Tests\Functional\Api\User;

use Symfony\Component\HttpFoundation\JsonResponse;

class GetUserGroupTest extends UserTestBase
{
    public function testGetUserGroups(): void
    {
        self::$admin->request('GET', \sprintf('%s/%s/groups.%s', $this->endpoint, self::IDS['admin_id'], self::FORMAT));

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertCount(1, $responseData['hydra:member']);
    }

    public function testGetAnotherUserGroups(): void
    {
        self::$user->request('GET', \sprintf('%s/%s/groups.%s', $this->endpoint, self::IDS['admin_id'], self::FORMAT));

        $response = self::$user->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertCount(0, $responseData['hydra:member']);
    }
}
