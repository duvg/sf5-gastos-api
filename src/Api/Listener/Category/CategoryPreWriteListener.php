<?php

declare(strict_types=1);

namespace App\Api\Listener\Category;

use App\Api\Listener\PreWriteListener;
use App\Entity\Category;
use App\Entity\User;
use App\Exception\Common\CannotAddAnotherUserAsOwnerException;
use App\Exception\Group\UserNotMemberOfGroupException;
use App\Repository\GroupRepository;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CategoryPreWriteListener implements PreWriteListener
{
    private const POST_CATEGORY = 'api_categories_post_collection';

    private TokenStorageInterface $tokenStorage;

    private GroupRepository $groupRepository;

    public function __construct(TokenStorageInterface $tokenStorage, GroupRepository $groupRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->groupRepository = $groupRepository;
    }

    public function onKernelView(ViewEvent $event): void
    {

        $request = $event->getRequest();

        if (self::POST_CATEGORY === $request->get('_route')) {
            /** @var User $tokenUser */
            $tokenUser = $this->tokenStorage->getToken()->getUser();

            /** @var Category $category */
            $category = $event->getControllerResult();

            if (null !== $category->getGroup()) {
                if (!$this->groupRepository->userIsMember($category->getGroup(), $tokenUser)) {
                    throw UserNotMemberOfGroupException::create();
                }

                if ($category->getUser()->getId() !== $tokenUser->getId()) {
                    throw CannotAddAnotherUserAsOwnerException::create();
                }

                return;
            }

            if ($category->getUser()->getId() !== $tokenUser->getId()) {
                throw CannotAddAnotherUserAsOwnerException::create();
            }
        }
    }
}
