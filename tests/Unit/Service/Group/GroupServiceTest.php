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

    /**
     * @throws \Exception
     */
    public function testAddUserToGroup(): void
    {
        $groupId = 'group_id_123';
        $userId = 'user_id_432';

        $user = new User('user', 'user@api.com');
        $newUser = new User('new', 'new.user@api.com');
        $group = new Group('group', $user);

        $this->groupRepository
            ->expects($this->exactly(1))
            ->method('findOneById')
            ->with($groupId)
            ->willReturn($group);

        $this->groupRepository
            ->expects($this->exactly(2))
            ->method('userIsMember')
            ->with($this->isType('object'), $this->isType('object'))
            ->willReturn(true, false);

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneById')
            ->with($userId)
            ->willReturn($newUser);


        $this->groupRepository
            ->expects($this->exactly(1))
            ->method('save')
            ->with($this->isType('object'))
            ->willReturn(true);

        $this->groupService->addUserToGroup($groupId, $userId, $user);
    }

    /**
     * @throws \Exception
     */
    public function testRemoveUserFromGroup()
    {
        $groupId = 'group_id_123';
        $userId = 'user_id_432';

        $user = new User('user', 'user@api.com');
        $newUser = new User('new', 'new.user@api.com');
        $group = new Group('group', $user);

        $this->groupRepository
            ->expects($this->exactly(1))
            ->method('findOneById')
            ->with($groupId)
            ->willReturn($group);

        $this->groupRepository
            ->expects($this->exactly(2))
            ->method('userIsMember')
            ->with($this->isType('object'), $this->isType('object'))
            ->willReturn(true, true);

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneById')
            ->with($userId)
            ->willReturn($newUser);



        $this->groupRepository
            ->expects($this->exactly(1))
            ->method('save')
            ->with($this->isType('object'))
            ->willReturn(true);

        $this->groupService->removeUserFromGroup($groupId, $userId, $user);
    }

    public function testGroupNotExist(): void
    {
        $groupId = 'group_id_123';
        $userId = 'user_id_432';

        $user = new User('user', 'user@api.com');
        $group = new Group('group', $user);

        $this->groupRepository
            ->expects($this->exactly(1))
            ->method('findOneById')
            ->with($groupId)
            ->willReturn(null);

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

        $this->groupRepository
            ->expects($this->exactly(1))
            ->method('findOneById')
            ->with($groupId)
            ->willReturn($group);

        $this->groupRepository
            ->expects($this->exactly(1))
            ->method('userIsMember')
            ->with($this->isType('object'), $this->isType('object'))
            ->willReturn(false);

        $this->expectException(CannotAddUsersToGroupException::class);

        $this->groupService->addUserToGroup($groupId, $userId, $user);
    }

    public function testUserToAddNotExist(): void
    {
        $groupId = 'group_id_123';
        $userId = 'user_id_432';

        $user = new User('user', 'user@api.com');
        $group = new Group('group', $user);

        $this->groupRepository
            ->expects($this->exactly(1))
            ->method('findOneById')
            ->with($groupId)
            ->willReturn($group);

        $this->groupRepository
            ->expects($this->exactly(1))
            ->method('userIsMember')
            ->with($this->isType('object'), $this->isType('object'))
            ->willReturn(true);

        $this->userRepository
            ->expects($this->exactly(1))
            ->method('findOneById')
            ->with($userId)
            ->willReturn(null);

        $this->expectException(UserDoesNotExistException::class);

        $this->groupService->addUserToGroup($groupId, $userId, $user);
    }
}
