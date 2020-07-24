<?php

declare(strict_types=1);

namespace App\Api\Action\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Api\Action\RequestTransformer;
use App\Service\Password\EncoderService;
use Symfony\Component\HttpFoundation\Request;
use App\Exception\User\UserAlreadyExistException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class Register
{
    private UserRepository $userRepository;

    private EncoderFactoryInterface $encoderFactory;

    public function __construct(UserRepository $userRepository, EncoderService $encoderService)
    {
        $this->userRepository = $userRepository;
        $this->encoderService = $encoderService;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): User
    {
        $name = RequestTransformer::getRequiredField($request, 'name');
        $email = RequestTransformer::getRequiredField($request, 'email');
        $password = RequestTransformer::getRequiredField($request, 'password');

        $existingUser = $this->userRepository->findOneByEmail($email);

        if (null !== $existingUser) {
            throw UserAlreadyExistException::fromUserEmail($email);
        }

        // Create user if not exist
        $user = new User($name, $email);

        $user->setPassword($this->encoderService->generateEncodedPasswordForUser($user, $password));

        $this->userRepository->save($user);

        return $user;
    }
}
