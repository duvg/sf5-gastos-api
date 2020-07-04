<?php

declare(strict_types=1);

namespace App\Api\Action\Group;

use App\Api\Action\RequestTransformer;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AddUser
{
    private UserRepository $userRepository;

    private GroupRepository $groupRepository;

    public function __construct(UserRepository $userRepository, GroupRepository $groupRepository)
    {
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @Route("/groups/add_user", methods={"POST"})
     */
    public function __invoke(Request $request, User $user): JsonResponse
    {
        $groupId = RequestTransformer::getRequiredField($request, 'group_id');
        $userId = RequestTransformer::getRequiredField($request, 'user_id');

        // Check if user y member of group
        $group = $this->groupRepository->findOneById($groupId);
        if (null === $group) {
            throw new BadRequestHttpException('Group not found');
        }

        if (!$this->groupRepository->userIsMember($group, $user)) {
            throw new BadRequestHttpException('You cannot add user to this group!');
        }

        $newUser = $this->userRepository->findOneById($userId);

        if (null === $newUser) {
            throw new BadRequestHttpException('User not found');
        }

        // Check user is not member
        if ($this->groupRepository->userIsMember($group, $newUser)) {
            throw new ConflictHttpException('This user is already of this group');
        }

        $group->addUser($newUser);

        $this->groupRepository->save($group);

        return new JsonResponse([
            'message' => \sprintf(
                'User with id: %s has been added to group with id %s',
                $newUser->getId(),
                $group->getId()
            ),
        ]);
    }
}
