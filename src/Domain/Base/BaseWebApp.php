<?php

namespace ZnSf\Web\Domain\Base;

use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouteCollection;
use ZnCore\Base\App\Base\BaseApp;
use ZnCore\Base\Container\Interfaces\ContainerConfiguratorInterface;
use ZnCore\Base\EventDispatcher\Interfaces\EventDispatcherConfiguratorInterface;
use ZnLib\Web\View\View;
use ZnSf\Web\Domain\Subscribers\FindRouteSubscriber;
use ZnSf\Web\Domain\Subscribers\WebDetectTestEnvSubscriber;
use ZnSf\Web\Domain\Subscribers\WebFirewallSubscriber;

abstract class BaseWebApp extends BaseApp
{

    public function appName(): string
    {
        return 'web';
    }

    public function subscribes(): array
    {
        return [
            WebDetectTestEnvSubscriber::class,
        ];
    }

    public function import(): array
    {
        return ['i18next', 'container', 'rbac', 'symfonyWeb'];
    }

    protected function configDispatcher(EventDispatcherConfiguratorInterface $configurator): void
    {
        $configurator->addSubscriber(FindRouteSubscriber::class);
        $configurator->addSubscriber(WebFirewallSubscriber::class);
    }

    protected function configContainer(ContainerConfiguratorInterface $containerConfigurator): void
    {
        $containerConfigurator->singleton(HttpKernelInterface::class, HttpKernel::class);
        $containerConfigurator->bind(ErrorRendererInterface::class, HtmlErrorRenderer::class);
        $containerConfigurator->singleton(View::class, View::class);
        $containerConfigurator->singleton(RouteCollection::class, RouteCollection::class);
//        $containerConfigurator->singleton('error_controller', ErrorController::class);
    }
}
