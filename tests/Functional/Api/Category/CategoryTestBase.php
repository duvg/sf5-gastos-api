<?php

namespace App\Tests\Functional\Api\Category;

use App\Tests\Functional\TestBase;

class CategoryTestBase extends TestBase
{
    protected string $endpoint;

    public function setUp(): void
    {
        parent::setUp();

        $this->endpoint = '/api/v1/categories';
    }
}
