<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security\Validator\Role;

use App\Exception\Role\RequiredRoleToAddRoleAdminNotFoundException;
use App\Security\Role;
use App\Security\Validator\Role\CanAddRoleAdmin;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class CanAddRoleAdminTest extends TestCase
{
    /** @var Security|MockObject */
    private Security $security;

    private CanAddRoleAdmin $validator;

    public function setUp(): void
    {
        $this->security = $this->getMockBuilder(Security::class)
            ->disableOriginalConstructor()->getMock();

        $this->validator = new CanAddRoleAdmin($this->security);
    }

    public function testCanAddRoleAdmin(): void
    {
        $payload = [
            'roles' => [
                Role::ROLE_ADMIN,
                Role::ROLE_USER,
            ],
        ];

        $request = new Request([], [], [], [], [], [], \json_encode($payload));

        $this->security
            ->expects($this->exactly(1))
            ->method('isGranted')
            ->with(Role::ROLE_ADMIN)
            ->willReturn(true);

        $response = $this->validator->validate($request);

        $this->assertIsArray($response);
    }

    public function testCannotAddRoleAdmin(): void
    {
        $payload = [
            'roles' => [
                Role::ROLE_ADMIN,
                Role::ROLE_USER,
            ],
        ];

        $request = new Request([], [], [], [], [], [], \json_encode($payload));

        $this->security
            ->expects($this->exactly(1))
            ->method('isGranted')
            ->with(Role::ROLE_ADMIN)
            ->willReturn(false);

        $this->expectException(RequiredRoleToAddRoleAdminNotFoundException::class);
        $this->expectExceptionMessage('ROLE_ADMIN require to perform this operation');

        $this->validator->validate($request);
    }
}
