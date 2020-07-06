<?php

namespace App\Tests\Unit\Api\Action\Group;

use App\Api\Action\Group\AddUser;
use App\Entity\Group;
use App\Entity\User;
use App\Exception\Group\CannotAddUsersToGroupException;
use App\Exception\Group\GroupDoesNotExistException;
use App\Exception\Group\UserAlreadyMemberOfGroupException;
use App\Exception\Group\UserDoesNotExistException;
use App\Tests\Unit\TestBase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AddUserTest extends TestBase
{
    private User $user;

    private User $newUser;

    private array $payload;

    private Request $request;

    private AddUser $action;

    public function setUp()
    {
        parent::setUp();

        $this->user = new User('user', 'user@api.com');
        $this->newUser = new User('new ser', 'new.user@api.com');

        $this->payload = [
            'group_id' => 'group_id_123',
            'user_id' => 'user_id_543',
        ];

        $this->request = new Request([], [], [], [], [], [], \json_encode($this->payload));

        $this->action = new AddUser($this->userRepository, $this->groupRepository);
    }

    /**
     * @throws \Exception
     */
    public function testCanAddUserToGroup(): void
    {
        $group = new Group('group', $this->user);

        $this->groupRepositoryProphecy->findOneById($this->payload['group_id'])->willReturn($group);
        $this->groupRepositoryProphecy->userIsMember($group, $this->user)->willReturn(true);
        $this->userRepositoryProphecy->findOneById($this->payload['user_id'])->willReturn($this->newUser);
        $this->groupRepositoryProphecy->userIsMember($group, $this->newUser)->willReturn(false);

        $this->groupRepositoryProphecy->save(
            Argument::that(function (Group $group): bool {
                return true;
            })
        )->shouldBeCalledOnce();

        $response = $this->action->__invoke($this->request, $this->user);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    public function testForNonExistingGroup(): void
    {
        $this->groupRepositoryProphecy->findOneById($this->payload['group_id'])->willReturn(null);

        $this->expectException(GroupDoesNotExistException::class);

        $this->action->__invoke($this->request, $this->user);
    }

    /**
     * @throws \Exception
     */
    public function testAddUserToAnotherGroup(): void
    {
        $group = new Group('group', $this->user);

        $this->groupRepositoryProphecy->findOneById($this->payload['group_id'])->willReturn($group);
        $this->groupRepositoryProphecy->userIsMember($group, $this->user)->willReturn(false);

        $this->expectException(CannotAddUsersToGroupException::class);

        $this->action->__invoke($this->request, $this->user);
    }

    /**
     * @throws \Exception
     */
    public function testNewUserDoesNotExist(): void
    {
        $group = new Group('group', $this->user);

        $this->groupRepositoryProphecy->findOneById($this->payload['group_id'])->willReturn($group);
        $this->groupRepositoryProphecy->userIsMember($group, $this->user)->willReturn(true);
        $this->userRepositoryProphecy->findOneById($this->payload['user_id'])->willReturn(null);

        $this->expectException(UserDoesNotExistException::class);

        $this->action->__invoke($this->request, $this->user);
    }

    public function testUserAlreadyMemberOfGroup(): void
    {
        $group = new Group('group', $this->user);

        $this->groupRepositoryProphecy->findOneById($this->payload['group_id'])->willREturn($group);
        $this->groupRepositoryProphecy->userIsMember($group, $this->user)->willReturn(true);
        $this->userRepositoryProphecy->findOneById($this->payload['user_id'])->willReturn($this->newUser);
        $this->groupRepositoryProphecy->userIsMember($group, $this->newUser)->willReturn(true);

        $this->expectException(UserAlreadyMemberOfGroupException::class);

        $this->action->__invoke($this->request, $this->user);
    }
}
