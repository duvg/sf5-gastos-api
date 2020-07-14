<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Expense;

use App\Tests\Functional\TestBase;

class ExpenseTestBase extends TestBase
{
    protected string $endpoint;

    public function setUp(): void
    {
        parent::setUp();

        $this->endpoint = '/api/v1/expenses';
    }
}
