<?php

declare(strict_types=1);

namespace App\Exception\Group;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CannotAddUsersToGroupException extends AccessDeniedHttpException
{
    private const MESSAGE = 'You cannot add user to this group!';

    public static function create(): self
    {
        throw new self(self::MESSAGE);
    }
}
