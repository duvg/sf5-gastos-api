<?php

declare(strict_types=1);

namespace App\Entity;

class Category
{
    private string $id;

    private string $name;

    private ?User $user;

    private ?Group $group;

    private ?\DateTime $createdAt = null;

    private ?\DateTime $updatedAt = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function markAsUpdated(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function isOwnedByUser(User $user): bool
    {
        if (null !== $this->getUser()) {
            return $this->getUser()->getId() === $user->getId();
        }

        return false;
    }
}
