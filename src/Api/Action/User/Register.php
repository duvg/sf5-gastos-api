<?php

declare(strict_types=1);

namespace App\Api\Action\User;

use App\Api\Action\RequestTransformer;
use App\Entity\User;
use App\Exception\User\UserAlreadyExistException;
use App\Repository\UserRepository;
use App\Service\Password\EncoderService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class Register
{
    private UserRepository $userRepository;

    private JWTTokenManagerInterface $JWTTokenManager;

    private EncoderFactoryInterface $encoderFactory;
    /**
     * @var EncoderService
     */
    private $encoderService;

    public function __construct(UserRepository $userRepository, JWTTokenManagerInterface $JWTTokenManager, EncoderService $encoderService)
    {
        $this->userRepository = $userRepository;
        $this->JWTTokenManager = $JWTTokenManager;
        $this->encoderService = $encoderService;
    }

    /**
     * @Route("/users/register", methods={"POST"})
     *
     * @throws \Exception
     */
    public function __invoke(Request $request): JsonResponse
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

        // Generate Token
        $jwt = $this->JWTTokenManager->create($user);

        return new JsonResponse(['token' => $jwt]);
    }
}
