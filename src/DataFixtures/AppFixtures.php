<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Expense;
use App\Entity\Group;
use App\Entity\User;
use App\Security\Role;
use App\Service\Password\EncoderService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private EncoderService $encoderService;

    public function __construct(EncoderService $encoderService)
    {
        $this->encoderService = $encoderService;
    }

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $users = $this->getUsers();
        foreach ($users as $userData) {
            $user = new User($userData['name'], $userData['email'], $userData['id']);
            $user->setPassword($this->encoderService->generateEncodedPasswordForUser($user, $userData['password']));
            $user->setRoles($userData['roles']);
            $manager->persist($user);

            foreach ($userData['categories'] as $categoryData) {
                $category = new Category($categoryData['name'], $user, null, $categoryData['id']);

                $manager->persist($category);

                foreach ($categoryData['expenses'] as $expenseData) {
                    $expense = new Expense(
                        $category,
                        $user,
                        $expenseData['amount'],
                        $expenseData['description'],
                        null,
                        $expenseData['id']
                    );

                    $manager->persist($expense);
                }
            }

            foreach ($userData['groups'] as $groupData) {
                $group = new Group($groupData['name'], $user, $groupData['id']);
                $group->addUser($user);

                $manager->persist($group);

                foreach ($groupData['categories'] as $categoryData) {
                    $category = new Category($categoryData['name'], $user, $group, $categoryData['id']);

                    $manager->persist($category);

                    foreach ($categoryData['expenses'] as $expenseData) {
                        $expense = new Expense(
                            $category,
                            $user,
                            $expenseData['amount'],
                            $expenseData['description'],
                            $group,
                            $expenseData['id']
                        );

                        $manager->persist($expense);
                    }
                }
            }
        }

        $manager->flush();
    }

    private function getUsers(): array
    {
        return [
            [
                'id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97001',
                'name' => 'Admin',
                'email' => 'admin@api.com',
                'password' => 'password',
                 'roles' => [
                     Role::ROLE_ADMIN,
                     Role::ROLE_USER,
                 ],
                'groups' => [
                    [
                        'id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97003',
                        'name' => 'Admin\'s Group',
                        'categories' => [
                            [
                                'id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97007',
                                'name' => 'Admin\'s Group category',
                                'expenses' => [
                                    [
                                        'id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97011',
                                        'amount' => 200,
                                        'description' => 'Admin\'s group expense description',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'categories' => [
                    [
                        'id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97005',
                        'name' => 'Admin\'s Category',
                        'expenses' => [
                            [
                                'id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97009',
                                'amount' => 100,
                                'description' => 'Admin expense description',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97002',
                'name' => 'user',
                'email' => 'user@api.com',
                'password' => 'password',
                'roles' => [
                    Role::ROLE_USER,
                ],
                'groups' => [
                    [
                        'id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97004',
                        'name' => 'User\'s Group',
                        'categories' => [
                            [
                                'id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97008',
                                'name' => 'User\'s Group category',
                                'expenses' => [
                                    [
                                        'id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97012',
                                        'amount' => 400,
                                        'description' => 'User\'s group expense description',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'categories' => [
                    [
                        'id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97006',
                        'name' => 'User\'s Category',
                        'expenses' => [
                            [
                                'id' => 'd781a25f-da6c-42ee-9261-7cc8ddd97010',
                                'amount' => 300,
                                'description' => 'User expense description',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
