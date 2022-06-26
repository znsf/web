<?php

use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use ZnSf\Web\Domain\Libs\ControllerResolver;
use ZnLib\Web\View\Resources\Css;
use ZnLib\Web\View\Resources\Js;
use ZnLib\Web\View\View;

return [
    'singletons' => [
        ControllerResolverInterface::class => ControllerResolver::class,
        View::class => View::class,
        Css::class => Css::class,
        Js::class => Js::class,
    ],
];
