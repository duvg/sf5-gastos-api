<?php

declare(strict_types=1);

namespace App\Api\Listener;


use http\Client\Curl\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    // Add data to token
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        $payload = $event->getData();
        $payload['name'] = $user->getName();
        $payload['email'] = $user->getEmail();
        $payload['createdAt'] = $user->getCreatedAt();
        unset($payload['roles']);

        $event->setData($payload);


    }
}