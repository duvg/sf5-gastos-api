<?php

namespace App\Tests\Functional\Api\Category;

use Symfony\Component\HttpFoundation\JsonResponse;

class PutCategoryTest extends CategoryTestBase
{
    public function testPutCategory(): void
    {
        $payload = ['name' => 'new name category'];

        self::$admin->request(
            'PUT',
            \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_category_id'], self::FORMAT),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$admin->getResponse();
        $responseData = $this->getResponseData($response);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($payload['name'], $responseData['name']);
    }

    public function testPutAnotherCategory(): void
    {
        $payload = ['name' => 'new category name'];

        self::$user->request(
            'PUT',
            \sprintf('%s/%s.%s', $this->endpoint, self::IDS['admin_category_id'], self::FORMAT),
            [],
            [],
            [],
            \json_encode($payload)
        );

        $response = self::$user->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
