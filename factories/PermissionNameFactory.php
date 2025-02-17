<?php

namespace app\factories;

use app\auth\PermissionNameFactoryInterface;
use app\values\UserAction;

/**
 * @inheritDoc
 */
class PermissionNameFactory implements PermissionNameFactoryInterface
{
    /**
     * @inheritDoc
     *
     * @param string $category
     * @param string|UserAction $action
     *
     * @return string
     */
    public function getName(string $category, string|UserAction $action): string
    {
        if (!is_string($action)) {
            $action = $action->value;
        }

        return $action . ucfirst($category);
    }
}
