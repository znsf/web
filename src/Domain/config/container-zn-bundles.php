<?php

return [
    'singletons' => [
        'ZnBundle\Notify\Domain\Interfaces\Repositories\ToastrRepositoryInterface' => 'ZnBundle\Notify\Domain\Repositories\Symfony\ToastrRepository',
        'ZnBundle\\Language\\Domain\\Interfaces\\Repositories\\SwitchRepositoryInterface' => 'ZnBundle\\Language\\Domain\\Repositories\\Symfony4\\SwitchRepository',
        'ZnBundle\\Language\\Domain\\Interfaces\\Repositories\\StorageRepositoryInterface' => 'ZnBundle\\Language\\Domain\\Repositories\\Symfony4\\StorageRepository',
    ],
];
