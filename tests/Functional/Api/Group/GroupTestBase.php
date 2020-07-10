<?php

namespace App\Tests\Functional\Api\Group;

use App\Tests\Functional\TestBase;

class GroupTestBase extends TestBase
{
    protected string $endpoint;

    public function setUp(): void
    {
        parent::setUp();

        $this->endpoint = '/api/v1/groups';
    }
}
