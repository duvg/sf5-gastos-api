<?php

namespace App\Tests\Functional\Api\Group;

use Symfony\Component\HttpFoundation\JsonResponse;

class GetGroupTest extends GroupTestBase
{
    public function testGetGroupsForAdmin(): void
    {
        self::$admin->request('GET', \sprintf('%s.%s', $this->endpoint, self::FORMAT));

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertCount(2, $responseData['hydra:member']);
    }

    public function testGetgRoupForUser(): void
    {
        self::$user->request('GET', \sprintf('%s.%s', $this->endpoint, self::FORMAT));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testGetUserGroupWithAdmin(): void
    {
        self::$admin->request('GET', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['user_group_id'], self::FORMAT));

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(self::IDS['user_group_id'], $responseData['id']);
    }

    public function testGetUserGroupWithUser(): void
    {
        self::$user->request('GET', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_group_id'], self::FORMAT));

        $response = self::$user->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
