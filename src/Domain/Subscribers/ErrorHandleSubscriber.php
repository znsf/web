<?php

namespace ZnSf\Web\Domain\Subscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use ZnLib\Web\Components\View\Libs\View;
use ZnSf\Web\Domain\Libs\CallAction;
use ZnSf\Web\Symfony4\Controllers\ErrorController2;

class ErrorHandleSubscriber implements EventSubscriberInterface
{

    private $callAction;
    private $layout;
    private $layoutParams = [];
    private $view;

    public function __construct(
        CallAction $callAction,
        View $view
    )
    {
        $this->callAction = $callAction;
        $this->view = $view;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function getLayoutParams(): array
    {
        return $this->layoutParams;
    }

    public function setLayoutParams(array $layoutParams): void
    {
        $this->layoutParams = $layoutParams;
    }

    public function addLayoutParam(string $name, $value): void
    {
        $this->layoutParams[$name] = $value;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $request = $event->getRequest()->duplicate();
        $request->attributes->set('_controller', ErrorController2::class);
        $request->attributes->set('_action', 'handleError');

        $arguments = [
            $request,
            $event->getThrowable(),
        ];
        $response = $this->callAction->call($request, $arguments);
        $this->wrapContent($response);
        $event->setResponse($response);
        $event->stopPropagation();
    }

    private function wrapContent(Response $response): void
    {
        $params = $this->getLayoutParams();
        $params['content'] = $response->getContent();
//        $view = ContainerHelper::getContainer()->get(View::class);
        $content = $this->view->renderFile($this->layout, $params);
        $response->setContent($content);
    }
}
