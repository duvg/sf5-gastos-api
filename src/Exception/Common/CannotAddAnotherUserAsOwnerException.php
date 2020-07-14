<?php

declare(strict_types=1);

namespace App\Exception\Common;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CannotAddAnotherUserAsOwnerException extends AccessDeniedHttpException
{
    private const MESSAGE = 'You cannot add another user as owner of this resource';

    public static function create(): self
    {
        throw new self(self::MESSAGE);
    }
}
