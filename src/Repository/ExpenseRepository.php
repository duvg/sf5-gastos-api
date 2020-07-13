<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Expense;

class ExpenseRepository extends BaseRepository
{
    protected static function entityClass(): string
    {
        return Expense::class;
    }
}
