<?php

namespace AppBundle\Listener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use AppBundle\Entity\User;

class LoginListener {

    private $doctrine, $authChecker, $token;

    public function __construct(Registry $doctrine, AuthorizationChecker $authChecker, TokenStorage $token) {
        $this->doctrine = $doctrine;
        $this->authChecker = $authChecker;
        $this->token = $token;
    }

}

?>
