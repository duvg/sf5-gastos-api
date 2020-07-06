<?php

namespace App\Service\Group;

use App\Entity\Group;
use App\Entity\User;
use App\Exception\Group\CannotAddUsersToGroupException;
use App\Exception\Group\GroupDoesNotExistException;
use App\Exception\Group\UserAlreadyMemberOfGroupException;
use App\Exception\Group\UserDoesNotExistException;
use App\Exception\Group\UserNotMemberOfGroupException;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;

class GroupService
{
    private GroupRepository $groupRepository;

    private UserRepository $userRepository;

    public function __construct(GroupRepository $groupRepository, UserRepository $userRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
    }

    // Add user to group
    public function addUserToGroup(string $groupId, string $userId, User $user): void
    {
        $group = $this->getGroupFromId($groupId);

        // Check if user can manage the group
        $this->userCanManageGroup($user, $group);

        $userToAdd = $this->getUserFromId($userId);

        // Check if user is not member
        $this->checkUserIsMemberOlAlreadyExistInGroup($group, $userToAdd, 'add');

        $group->addUser($userToAdd);

        $this->groupRepository->save($group);
    }

    // Remove user from group
    public function removeUserFromGroup(string $groupid, string $userId, User $user): void
    {
        $group = $this->getGroupFromId($groupid);

        $this->userCanManageGroup($user, $group);

        $userToRemove = $this->getUserFromId($userId);

        $this->checkUserIsMemberOlAlreadyExistInGroup($group, $userToRemove, 'remove');

        $group->removeUser($userToRemove);

        $this->groupRepository->save($group);
    }

    private function getGroupFromId(string $groupId): Group
    {
        if (null !== $group = $this->groupRepository->findOneById($groupId)) {
            return $group;
        }
        throw GroupDoesNotExistException::fromGroupId($groupId);
    }

    private function userCanManageGroup(User $user, Group $group): void
    {
        if (!$this->groupRepository->userIsMember($group, $user)) {
            throw CannotAddUsersToGroupException::create();
        }
    }

    private function getUserFromId(string $userId): User
    {
        // Check if user exist
        if (null !== $user = $this->userRepository->findOneById($userId)) {
            return $user;
        }

        throw UserDoesNotExistException::fromUserId($userId);
    }

    private function checkUserIsMemberOlAlreadyExistInGroup(Group $group, User $user, string $type): void
    {
        if (!$this->groupRepository->userIsMember($group, $user)) {
            if ('add' === type) {
                throw UserAlreadyMemberOfGroupException::fromUserId($user->getId());
            }

            if ('remove' === $type) {
                throw UserNotMemberOfGroupException::create();
            }
        }
    }
}
