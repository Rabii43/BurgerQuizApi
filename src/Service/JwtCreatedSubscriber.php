<?php

namespace App\Service;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedSubscriber
{
    public function updateJwtData(JWTCreatedEvent $event)
    {
        // 1. Récupérer l'utilisateur (pour avoir son firstName et lastName)
        $user = $event->getUser();

        // 2. Enrichir les data pour qu'elles contiennent ces données
        $data = $event->getData();
        $data['username'] = $user->getUsername();
        $data['id'] = $user->getId();
        $data['email'] = $user->getEmail();
        $data['avatar'] = $user->getAvatar();
        $data['roles'] = implode(",", $user->getRoles());

        $event->setData($data);
    }
}
