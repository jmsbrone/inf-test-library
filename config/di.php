<?php

use app\auth\PermissionNameFactoryInterface;
use app\factories\PermissionNameFactory;
use app\notifications\NewBookNotifierInterface;
use app\notifications\provider\NewBookSMSNotifier;

return [
    PermissionNameFactoryInterface::class => PermissionNameFactory::class,
    NewBookNotifierInterface::class => NewBookSMSNotifier::class,
];
