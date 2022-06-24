<?php

namespace ZnSf\Web\Domain\Subscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;
use ZnCore\Base\App\Enums\AppEventEnum;

class WebDetectTestEnvSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            AppEventEnum::BEFORE_INIT_ENV => 'onBeforeInitEnv',
        ];
    }

    public function onBeforeInitEnv(Event $event)
    {
$envDetector = new \ZnLib\Web\Libs\EnvDetector\WebEnvDetector();
//$envDetector = new \ZnCore\Base\App\Libs\EnvDetector\ConsoleEnvDetector();
//        $envDetector = new \ZnCore\Base\App\Libs\EnvDetector\EnvDetector();
        $isTest = $envDetector->isTest();
//        global $_GET, $_SERVER;
//        $isTest = (isset($_SERVER['HTTP_ENV_NAME']) && $_SERVER['HTTP_ENV_NAME'] == 'test') || (isset($_GET['env']) && $_GET['env'] == 'test');
        if ($isTest) {
            $_ENV['APP_ENV'] = 'test';
        }
        $_ENV['APP_MODE'] = $isTest ? 'test' : 'main';
    }
}
