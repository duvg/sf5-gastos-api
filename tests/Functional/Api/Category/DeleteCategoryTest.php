<?php

namespace App\Tests\Functional\Api\Category;

use Symfony\Component\HttpFoundation\JsonResponse;

class DeleteCategoryTest extends CategoryTestBase
{
    public function testDeleteCategoryWithAdmin(): void
    {
        self::$admin->request('DELETE', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_category_id'], self::FORMAT));

        $response = self::$admin->getResponse();

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteCategoryWithUser(): void
    {
        self::$user->request('DELETE', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['user_category_id'], self::FORMAT));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function testDeleteAnotherCategoryUser(): void
    {
        self::$user->request('DELETE', \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_category_id'], self::FORMAT));

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
