<?php

namespace ZnSf\Web\Symfony4\Controllers;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use ZnBundle\User\Symfony4\Web\Enums\WebUserEnum;
use ZnCore\Contract\Common\Exceptions\InvalidConfigException;
use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnCore\Base\Env\Helpers\EnvHelper;
use ZnCore\Contract\User\Exceptions\ForbiddenException;
use ZnCore\Contract\User\Exceptions\UnauthorizedException;
use ZnLib\Web\Symfony4\MicroApp\BaseWebController;
use ZnSf\Web\Symfony4\Interfaces\ErrorControllerInterface;

class ErrorController2 extends BaseWebController implements ErrorControllerInterface
{

    protected $layout = null;
    protected $viewsDir = __DIR__ . '/../views/error';
    private $session;
    private $logger;
    private $urlGenerator;

    public function __construct(
        SessionInterface $session,
        LoggerInterface $logger,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->session = $session;
        $this->logger = $logger;
        $this->urlGenerator = $urlGenerator;
    }

    public function handleError(Request $request, \Exception $exception): Response
    {
        $data = [
            'attributes' => $request->attributes->all(),
            'request' => $request->request->all(),
            'query' => $request->query->all(),
            'server' => $request->server->all(),
            'files' => $request->files->all(),
            'cookies' => $request->cookies->all(),
            'headers' => $request->headers->all(),
            'requestUri' => $request->getRequestUri(),
            'method' => $request->getMethod(),
        ];
        $logMessage = $exception->getMessage() ?: get_class($exception);
        $this->logger->error($logMessage, [
            'request' => $data,
            'trace' => debug_backtrace()
        ]);
        if ($exception instanceof ForbiddenException) {
            return $this->forbidden($request, $exception);
        }
        if ($exception instanceof UnauthorizedException) {
            return $this->unauthorized($request, $exception);
        }
        if ($exception instanceof NotFoundException) {
            return $this->notFound($request, $exception);
        }
        if ($exception instanceof ResourceNotFoundException) {
            return $this->notFound($request, $exception);
        }
        if ($exception instanceof InvalidConfigException) {
            return $this->commonRender('Config error', $exception->getMessage(), $exception);
        }
        return $this->commonRender('Error!', $exception->getMessage(), $exception);
    }

    private function commonRender(string $title, string $message, \Throwable $exception): Response
    {
        $params = [
            'title' => $title,
            'message' => $message,
        ];
        if (EnvHelper::isDebug()) {
            $params['exception'] = $exception;
        }
        return $this->render('handle-error', $params);
    }

    private function notFound(Request $request, \Exception $exception): Response
    {
        return $this->commonRender('Not found', 'Page not exists!', $exception);
    }

    private function unauthorized(Request $request, \Exception $exception): Response
    {
        $authUrl = $this->urlGenerator->generate('user/auth');
        if ($request->getRequestUri() == $authUrl) {
            return $this->commonRender('Unauthorized', 'Unauthorized!', $exception);
        }
        $this->session->set(WebUserEnum::UNAUTHORIZED_URL_SESSION_KEY, $request->getRequestUri());
        return $this->redirect($authUrl);
    }

    private function forbidden(Request $request, \Exception $exception): Response
    {
        return $this->commonRender('Forbidden', 'Access error', $exception);
    }
}
