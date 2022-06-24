<?php

namespace ZnSf\Web\Domain\Base;

use ZnSf\Web\Domain\Base\BaseWebApp;

abstract class BaseAdminApp extends BaseWebApp
{

    public function appName(): string
    {
        return 'admin';
    }

    public function import(): array
    {
        return ['i18next', 'container', 'rbac', 'symfonyAdmin'];
    }
}
