<?php

namespace ZnSf\Web\Domain\Libs;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class CallAction
{

    private $controllerResolver;
    private $argumentResolver;

    public function __construct(
        ControllerResolverInterface $controllerResolver,
        ArgumentResolverInterface $argumentResolver
    )
    {
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
    }

    public function call(Request $request, array $arguments = null): Response
    {
        $controller = $this->controllerResolver->getController($request);
        list($controllerInstance, $actionName) = $controller;
        if ($arguments === null) {
            $arguments = $this->argumentResolver->getArguments($request, $controller);
        }
        $response = $controller(...$arguments);
        return $response;
    }
}
