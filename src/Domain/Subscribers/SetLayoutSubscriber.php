<?php

namespace ZnSf\Web\Domain\Subscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use ZnCore\Base\Http\Enums\HttpStatusCodeEnum;
use ZnLib\Web\View\View;
use ZnLib\Web\Widgets\Alert\AlertWidget;
use ZnLib\Web\Widgets\BreadcrumbWidget;

class SetLayoutSubscriber implements EventSubscriberInterface
{

    private $layout;
    private $layoutParams = [];
    private $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        list($controllerInstance, $actionName) = $controller;
//        if (/*isset($this->layout) &&*/ $controllerInstance instanceof ControllerLayoutInterface) {
////            $controllerInstance->setLayout(null/*$this->layout*/);
////            $controllerInstance->setLayoutParams($this->getLayoutParams());
//        }
//        $controllerEvent = new ControllerEvent($controllerInstance, $actionName, $request);
//        $this->getEventDispatcher()->dispatch($controllerEvent, ControllerEventEnum::BEFORE_ACTION);
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $isAjax = $event->getRequest()->isXmlHttpRequest();

        $isWebResponse = get_class($response) == Response::class;

        if ($isAjax) {
//            sleep(1);
            $jsonResponse = new JsonResponse([
                //'title' => 'title',
                'url' => $event->getRequest()->getRequestUri(),
                'content' => [
                    'content' => $response->getContent(),
                    'breadcrumb' => BreadcrumbWidget::widget(),
                    'alert' => AlertWidget::widget(),
                    'runtime' => round(microtime(true) - MICRO_TIME, 3),
                ],
            ]);
            $event->setResponse($jsonResponse);
        } elseif ($isWebResponse && $response->getStatusCode() === HttpStatusCodeEnum::OK) {
            $this->wrapContent($response);
        }
    }

    public function getLayout(): ?string
    {
        return $this->layout;
    }

    public function setLayout(?string $layout): void
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

    private function wrapContent(Response $response): void
    {
        $params = $this->getLayoutParams();
        $params['content'] = $response->getContent();
//        $view = ContainerHelper::getContainer()->get(View::class);
        $content = $this->view->renderFile($this->layout, $params);
        $response->setContent($content);
    }
}
