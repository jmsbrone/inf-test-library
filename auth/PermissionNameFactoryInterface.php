<?php

namespace app\auth;

use app\values\UserAction;

/**
 * Генерация названий разрешений.
 */
interface PermissionNameFactoryInterface
{
    /**
     * Получение названия разрешения по категории и действию.
     *
     * @param string $category
     * @param UserAction|string $action
     *
     * @return string
     */
    public function getName(string $category, UserAction|string $action): string;
}
