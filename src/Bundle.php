<?php

namespace ZnSf\Web;

use ZnCore\Base\Bundle\Base\BaseBundle;

class Bundle extends BaseBundle
{

    public function deps(): array
    {
        return [
            \ZnLib\Web\Components\Form\Bundle::class,
            \ZnLib\Web\Components\View\Bundle::class,
            \ZnLib\Web\Components\Layout\Bundle::class,
        ];
    }
    
    public function container(): array
    {
        return [
            __DIR__ . '/Domain/config/container-symfony.php',
//            __DIR__ . '/Domain/config/container-zn-web.php',
//            __DIR__ . '/Domain/config/container-zn-bundles.php',
        ];
    }
}
