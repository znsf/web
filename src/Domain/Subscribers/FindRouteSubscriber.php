<?php

namespace ZnSf\Web\Domain\Subscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class FindRouteSubscriber implements EventSubscriberInterface
{

    private $matcher;

    public function __construct(
        UrlMatcher $matcher
    )
    {
        $this->matcher = $matcher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 128],
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        $uri = rtrim($request->getPathInfo(), '/');
        $attributes = $this->matcher->match($uri);
        if (is_array($attributes['_controller'])) {
            list($controllerClass, $actionName) = $attributes['_controller'];
        } else {
            $controllerClass = $attributes['_controller'];
            $actionName = $attributes['_action'];
        }
        $request->attributes->set('_controller', $controllerClass);
        $request->attributes->set('_action', $actionName);
    }
}
