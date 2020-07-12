<?php

namespace App\Exception\Common;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CannotAddAnotherUserAsOwnerException extends BadRequestHttpException
{
    private const MESSAGE = 'You cannot add another user as owner of this resource';

    public static function create(): self
    {
        throw new self(self::MESSAGE);
    }
}
