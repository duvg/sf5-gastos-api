<?php

declare(strict_types=1);

namespace App\Api\Listener\Expense;

use App\Api\Listener\PreWriteListener;
use App\Entity\Expense;
use App\Entity\User;
use App\Exception\Category\CannotAddCategoryException;
use App\Exception\Common\CannotAddAnotherUserAsOwnerException;
use App\Exception\Group\UserNotMemberOfGroupException;
use App\Repository\GroupRepository;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ExpensePreWriteListener implements PreWriteListener
{
    private const POST_EXPENSE = 'api_expenses_post_collection';
    private const PUT_EXPENSE = 'api_expenses_put_item';

    private TokenStorageInterface $tokenStorage;

    private GroupRepository $groupRepository;

    public function __construct(TokenStorageInterface $tokenStorage, GroupRepository $groupRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->groupRepository = $groupRepository;
    }

    public function onKernelView(ViewEvent $event): void
    {
        /** @var User $tokenUser */
        $tokenUser = $this->tokenStorage->getToken()->getUser();

        $request = $event->getRequest();

        if (self::POST_EXPENSE === $request->get('_route')) {
            /** @var Expense $expense */
            $expense = $event->getControllerResult();

            if (null !== $group = $expense->getGroup()) {
                if (!$this->groupRepository->userIsMember($group, $tokenUser)) {
                    throw UserNotMemberOfGroupException::create();
                }

                if ($expense->getUser()->getId() !== $tokenUser->getId()) {
                    throw CannotAddAnotherUserAsOwnerException::create();
                }

                if (!$expense->getCategory()->isOwnedByGroup($group)) {
                    throw CannotAddCategoryException::create();
                }
            }

            if ($expense->getUser()->getId() !== $tokenUser->getId()) {
                throw CannotAddAnotherUserAsOwnerException::create();
            }

            if (!$expense->getCategory()->isOwnedBy($tokenUser)) {
                throw CannotAddCategoryException::create();
            }
        }

        if (self::PUT_EXPENSE === $request->get('_route')) {
            /** @var Expense $expense */
            $expense = $event->getControllerResult();

            if (null !== $group = $expense->getGroup()) {
                if (!$expense->getCategory()->isOwnedByGroup($group)) {
                    throw CannotAddCategoryException::create();
                }
            }

            if (!$expense->getCategory()->isOwnedBy($tokenUser)) {
                throw CannotAddCategoryException::create();
            }
        }
    }
}
