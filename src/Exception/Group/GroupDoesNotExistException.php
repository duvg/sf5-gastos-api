<?php

namespace App\Exception\Group;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GroupDoesNotExistException extends BadRequestHttpException
{
    private const MESSAGE = 'Gruop with ID %s doesnot exist';

    public static function fromGroupId(string $groupId): self
    {
        throw new self(\sprintf(self::MESSAGE, $groupId));
    }
}
