<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api\Action\User;

use App\Entity\User;
use Prophecy\Argument;
use App\Tests\Unit\TestBase;
use App\Api\Action\User\Register;
use App\Service\Password\EncoderService;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use App\Exception\User\UserAlreadyExistException;


class RegisterTest extends TestBase
{
    /** @var EncoderService|MockObject */
    private $encoderService;

    private Register $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->encoderService = $this->getMockBuilder(EncoderService::class)
            ->disableOriginalConstructor()->getMock();

        $this->action = new Register($this->userRepository, $this->encoderService);
    }

    /**
     * @throws \Exception
     */
    public function testCreateUser(): void
    {
        $payload = [
            'name' => 'UserName',
            'email' => 'username@api.com',
            'password' => 'random_pass',
        ];

        $request = new Request([], [], [], [], [], [], \json_encode($payload));

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneByEmail')
            ->with($payload['email'])
            ->willReturn(null);

        $this->encoderService
            ->expects($this->exactly(1))
            ->method('generateEncodedPasswordForUser')
            ->with($this->isType('object'), $this->isType('string'));

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('save')
            ->with($this->isType('object'));

        $response = $this->action->__invoke($request);

        $this->assertInstanceOf(User::class, $response);
    }

    public function testCreateUserForExistingEmail(): void
    {
        $payload = [
            'name' => 'Username',
            'email' => 'username@api.com',
            'password' => 'random_pass',
        ];

        $request = new Request([], [], [], [], [], [], \json_encode($payload));

        $user = new User($payload['name'], $payload['email']);

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneByEmail')
            ->with($payload['email'])
            ->willReturn($user);

        $this->expectException(UserAlreadyExistException::class);

        $this->action->__invoke($request);
    }
}
