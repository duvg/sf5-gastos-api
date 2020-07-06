<?php

namespace App\Tests\Unit\Service\Group;

use App\Entity\Group;
use App\Entity\User;
use App\Exception\Group\CannotAddUsersToGroupException;
use App\Exception\Group\GroupDoesNotExistException;
use App\Exception\Group\UserDoesNotExistException;
use App\Service\Group\GroupService;
use App\Tests\Unit\TestBase;
use Prophecy\Argument;

class GroupServiceTest extends TestBase
{
    private GroupService $groupService;

    public function setUp(): void
    {
        parent::setUp();

        $this->groupService = new GroupService($this->groupRepository, $this->userRepository);
    }

    public function testAddUserToGroup(): void
    {
        $groupId = 'group_id_123';
        $userId = 'user_id_432';

        $user = new User('user', 'user@api.com');
        $newUser = new User('new', 'new.user@api.com');
        $group = new Group('group', $user);

        $this->groupRepositoryProphecy->findOneById($groupId)->willReturn($group);
        $this->groupRepositoryProphecy->userIsMember($group, $user)->willReturn(true);
        $this->userRepositoryProphecy->findOneById($userId)->willReturn($newUser);
        $this->groupRepositoryProphecy->userIsMember($group, $newUser)->willReturn(true);

        $this->groupRepositoryProphecy->save(
            Argument::that(function (Group $group): bool {
                return true;
            })
        )->shouldBeCalledOnce();

        $this->groupService->addUserToGroup($groupId, $userId, $user);
    }

    public function testRemoveUserFromGroup()
    {
        $groupId = 'group_id_123';
        $userId = 'user_id_432';

        $user = new User('user', 'user@api.com');
        $newUser = new User('new', 'new.user@api.com');
        $group = new Group('group', $user);

        $this->groupRepositoryProphecy->findOneById($groupId)->willReturn($group);
        $this->groupRepositoryProphecy->userIsMember($group, $user)->willReturn(true);
        $this->userRepositoryProphecy->findOneById($userId)->willReturn($newUser);
        $this->groupRepositoryProphecy->userIsMember($group, $newUser)->willReturn(true);

        $this->groupRepositoryProphecy->save(
            Argument::that(function (Group $group): bool {
                return true;
            })
        )->shouldBeCalledOnce();

        $this->groupService->removeUserFromGroup($groupId, $userId, $user);
    }

    public function testGroupNotExist(): void
    {
        $groupId = 'group_id_123';
        $userId = 'user_id:_432';

        $user = new User('user', 'user@api.com');
        $group = new Group('group', $user);

        $this->groupRepositoryProphecy->findOneById($groupId)->willReturn(null);
        $this->groupRepositoryProphecy->userIsMember($group, $user)->willReturn(true);

        $this->expectException(GroupDoesNotExistException::class);
        $this->groupService->addUserToGroup($groupId, $userId, $user);
    }

    public function testOwnerCannotAddUserOfGroup(): void
    {
        $groupId = 'group_id_123';
        $userId = 'user_id_432';

        $user = new User('user', 'user@api.com');
        $newUser = new User('new', 'new.user@api-.com');
        $group = new Group('group', $user);

        $this->groupRepositoryProphecy->findOneById($groupId)->willReturn($group);
        $this->groupRepositoryProphecy->userIsMember($group, $user)->willReturn(false);

        $this->expectException(CannotAddUsersToGroupException::class);

        $this->groupService->addUserToGroup($groupId, $userId, $user);
    }

    public function testUserToAddNotExist(): void
    {
        $groupId = 'group_id_123';
        $userId = 'user_id_432';

        $user = new User('user', 'user@api.com');
        $group = new Group('group', $user);

        $this->groupRepositoryProphecy->findOneById($groupId)->willReturn($group);
        $this->groupRepositoryProphecy->userIsMember($group, $user)->willReturn(true);
        $this->userRepositoryProphecy->findOneById($userId)->willReturn(null);

        $this->expectException(UserDoesNotExistException::class);

        $this->groupService->addUserToGroup($groupId, $userId, $user);
    }
}
