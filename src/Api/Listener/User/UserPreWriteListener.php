<?php

declare(strict_types=1);

namespace App\Api\Listener\User;

use App\Api\Action\RequestTransformer;
use App\Api\Listener\PreWriteListener;
use App\Entity\User;
use App\Security\Validator\Role\RoleValidator;
use App\Service\Password\EncoderService;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserPreWriteListener implements PreWriteListener
{
    private const PUT_USER = 'api_users_put_item';



    /** @var iterable|RoleValidator[] */
    private $roleValidators;
    /**
     * @var EncoderService
     */
    private $encoderService;

    public function __construct(iterable $roleValidators, EncoderService $encoderService)
    {
        $this->roleValidators = $roleValidators;
        $this->encoderService = $encoderService;
    }
    public function onKernelView(ViewEvent $event): void
    {
        $request = $event->getRequest();

        if (self::PUT_USER === $request->get('_route')) {
            /** @var User $user */
            $user = $event->getControllerResult();

            $roles = [];

            foreach ($this->roleValidators as $roleValidator) {
                $roles = $roleValidator->validate($request);
            }

            $user->setRoles($roles);

            $user->setPassword(
                $this->encoderService->generateEncodedPasswordForUser(
                    $user,
                    RequestTransformer::getRequiredField($request, 'password')
                ));
        }
    }
}