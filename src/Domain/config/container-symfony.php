<?php

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use ZnSf\Web\Domain\Libs\ControllerResolver;

return [
    'singletons' => [
        ControllerResolverInterface::class => ControllerResolver::class,
        ArgumentResolverInterface::class => ArgumentResolver::class,
        UrlGeneratorInterface::class => UrlGenerator::class,
        TokenStorageInterface::class => function (ContainerInterface $container) {
            $session = $container->get(SessionInterface::class);
            return new SessionTokenStorage($session);
        },
        SessionInterface::class => Session::class,
    ],
];
