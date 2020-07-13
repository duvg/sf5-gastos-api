<?php

declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\Expense;
use App\Entity\User;
use App\Security\Role;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ExpenseVoter extends BaseVoter
{
    private const EXPENSE_READ = 'EXPENSE_READ';
    private const EXPENSE_CREATE = 'EXPENSE_CREATE';
    private const EXPENSE_UPDATE = 'EXPENSE_UPDATE';
    private const EXPENSE_DELETE = 'EXPENSE_DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        return \in_array($attribute, $this->getSupportedAttributes(), true);
    }

    /**
     * @param Expense|null $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $tokenUser */
        $tokenUser = $token->getUser();

        if (self::EXPENSE_READ === $attribute) {
            if (null === $subject) {
                return $this->security->isGranted(Role::ROLE_ADMIN);
            }

            if (null !== $group = $subject->getGroup()) {
                return $this->security->isGranted(Role::ROLE_ADMIN)
                    || $this->groupRepository->userIsMember($group, $tokenUser);
            }

            return $this->security->isGranted(Role::ROLE_ADMIN)
                || $subject->isOwnedBy($tokenUser);
        }

        if (self::EXPENSE_CREATE == $attribute) {
            return true;
        }

        if (\in_array($attribute, [self::EXPENSE_UPDATE, self::EXPENSE_DELETE], true)) {
            if (null !== $group = $subject->getGroup()) {
                return  $this->security->isGranted(Role::ROLE_ADMIN)
                    || $this->groupRepository->userIsMember($group, $tokenUser);
            }

            return $this->security->isGranted(Role::ROLE_ADMIN)
                || $subject->isOwnedBy($tokenUser);
        }
    }

    private function getSupportedAttributes(): array
    {
        return [
            self::EXPENSE_READ,
            self::EXPENSE_CREATE,
            self::EXPENSE_UPDATE,
            self::EXPENSE_DELETE,
        ];
    }
}
