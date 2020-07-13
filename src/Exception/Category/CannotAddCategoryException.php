<?php

declare(strict_types=1);

namespace App\Exception\Category;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CannotAddCategoryException extends AccessDeniedHttpException
{
    private const  MESSAGE = 'You cannot add this category to this expense';

    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
