<?php

namespace ZnSf\Web\Domain\Subscribers;

use Symfony\Bundle\FrameworkBundle\Test\TestBrowserToken;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Security;
use ZnBundle\User\Domain\Enums\WebCookieEnum;
use ZnBundle\User\Domain\Interfaces\Services\AuthServiceInterface;
use ZnBundle\User\Domain\Interfaces\Services\IdentityServiceInterface;
use ZnCore\Contract\Common\Exceptions\InvalidConfigException;
use ZnCore\Base\Arr\Helpers\ArrayHelper;
use ZnCore\Base\DotEnv\Domain\Libs\DotEnv;
use ZnCore\Domain\Entity\Helpers\EntityHelper;
use ZnLib\Web\Symfony4\MicroApp\Interfaces\ControllerAccessInterface;
use ZnLib\Web\Symfony4\MicroApp\Libs\CookieValue;
use ZnUser\Rbac\Domain\Interfaces\Services\ManagerServiceInterface;

class WebFirewallSubscriber implements EventSubscriberInterface
{

    private $authService;
    private $identityService;
    private $session;
    private $security;
    private $managerService;

    public function __construct(
        AuthServiceInterface $authService,
        IdentityServiceInterface $identityService,
        ManagerServiceInterface $managerService,
        Security $security,
        SessionInterface $session
    )
    {
        $this->authService = $authService;
        $this->identityService = $identityService;
        $this->managerService = $managerService;
        $this->security = $security;
        $this->session = $session;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 128],
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(\Symfony\Component\HttpKernel\Event\ControllerEvent $event)
    {
        $controller = $event->getController();
        list($controllerInstance, $actionName) = $controller;

        if (!$controllerInstance instanceof ControllerAccessInterface) {
            //throw new InvalidConfigException('Controller not instance of "ControllerAccessInterface".');
        }
        if ($controllerInstance instanceof ControllerAccessInterface) {
            $access = $controllerInstance->access();
            $actionPermissions = ArrayHelper::getValue($access, $actionName);
            if (empty($actionPermissions)) {
                throw new InvalidConfigException('Empty permissions.');
            }
            if ($actionPermissions) {
                $this->managerService->checkMyAccess($actionPermissions);
            }
        }
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $token = new NullToken();
        $identityArray = $this->session->get('user.identity');
        if (!$identityArray) {
            $identityIdCookie = $event->getRequest()->cookies->get(WebCookieEnum::IDENTITY_ID);
            if ($identityIdCookie) {
                try {
                    $cookieValue = new CookieValue(DotEnv::get('CSRF_TOKEN_ID'));
                    $identityId = $cookieValue->decode($identityIdCookie);
                    $identity = $this->identityService->oneById($identityId);

                    $token = new TestBrowserToken([], $identity);

                    //$this->authService->setIdentity($identity);
                    $this->session->set('user.identity', EntityHelper::toArray($identity));
                } catch (\DomainException $e) {}
            }
        }
        $this->security->setToken($token);
    }
}
