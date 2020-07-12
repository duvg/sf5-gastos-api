<?php

namespace App\Tests\Functional\Api\Category;

use Symfony\Component\HttpFoundation\JsonResponse;

class GetCategoryTest extends CategoryTestBase
{
    public function testGetCategoriesForAdmin(): void
    {
        self::$admin->request('GET', \sprintf('%s.%s', $this->endpoint, self::FORMAT));

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertCount(2, $responseData['hydra:member']);
    }

    public function testGetCategoriesForUser(): void
    {
        self::$user->request('GET', \sprintf('%s.%s', $this->endpoint, self::FORMAT));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testGetUserCategoriesWithAdmin(): void
    {
        self::$admin->request('GET', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['user_category_id'], self::FORMAT));

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(self::IDS['user_category_id'], $responseData['id']);
    }

    public function testGetUserCategoriesWithUser(): void
    {
        self::$user->request('GET', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_category_id'], self::FORMAT));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
