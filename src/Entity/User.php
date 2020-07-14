<?php

declare(strict_types=1);

namespace App\Entity;

use App\Security\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    protected ?string $id;

    protected string $name;

    protected string $email;

    protected string $password;

    protected array $roles;

    protected bool $active;

    protected ?\DateTime $createdAt = null;

    protected ?\DateTime $updatedAt = null;

    /** @var Collection|Group[] */
    protected ?Collection $groups = null;

    /** @var Collection|Category[] */
    protected ?Collection $categories = null;

    /** @var Collection|Expense[] */
    protected ?Collection $expenses = null;

    /**
     * @throws \Exception
     */
    public function __construct(string $name, string $email, string $id = null)
    {
        $this->id = $id ?? Uuid::uuid4()->toString();
        $this->name = $name;
        $this->email = $email;
        $this->roles[] = Role::ROLE_USER;
        $this->active = true;
        $this->createdAt = new \DateTime();
        $this->groups = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->expenses = new ArrayCollection();
        $this->markAsUpdated();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function markAsUpdated(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getSalt(): void
    {
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
    }

    // Check if user is equals to user in token
    public function equals(User $user): bool
    {
        return $this->getId() === $user->getId();
    }

    /**
     * @return Collection|Group
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): void
    {
        $this->groups->add($group);
    }

    public function removeGroup(Group $group): void
    {
        $this->groups->removeElement($group);
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @return Collection|Expense[]
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
