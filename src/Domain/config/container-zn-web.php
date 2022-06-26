<?php

use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use ZnSf\Web\Domain\Libs\ControllerResolver;
use ZnLib\Web\Components\View\Resources\Css;
use ZnLib\Web\Components\View\Resources\Js;
use ZnLib\Web\Components\View\Libs\View;

return [
    'singletons' => [
        ControllerResolverInterface::class => ControllerResolver::class,
        View::class => View::class,
        Css::class => Css::class,
        Js::class => Js::class,
    ],
];
