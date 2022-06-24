<?php

use Psr\Container\ContainerInterface;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRegistry;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\Form\ResolvedFormTypeFactory;
use Symfony\Component\Form\ResolvedFormTypeFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

return [
    'singletons' => [
        ArgumentResolverInterface::class => ArgumentResolver::class,
        UrlGeneratorInterface::class => UrlGenerator::class,
        TokenStorageInterface::class => function (ContainerInterface $container) {
            $session = $container->get(SessionInterface::class);
            return new SessionTokenStorage($session);
        },
        SessionInterface::class => Session::class,
        CsrfTokenManagerInterface::class => CsrfTokenManager::class,
        ResolvedFormTypeFactoryInterface::class => ResolvedFormTypeFactory::class,
        FormFactoryInterface::class => FormFactory::class,
        FormRegistryInterface::class => function (ContainerInterface $container) {
            $extensions = [
                $container->get(HttpFoundationExtension::class)
            ];
            $resolvedFormTypeFactory = $container->get(ResolvedFormTypeFactoryInterface::class);
            $registry = new FormRegistry($extensions, $resolvedFormTypeFactory);
            return $registry;
        },
    ],
];
