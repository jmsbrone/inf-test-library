<?php

use app\auth\PermissionNameFactoryInterface;
use app\models\Author;
use app\models\Book;
use app\values\UserAction;
use yii\db\Migration;

/**
 * Class m250217_125047_add_permissions
 */
class m250217_125047_setup_roles_and_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $userRole = $auth->createRole('user');
        $auth->add($userRole);

        $categories = [
            Book::PERMISSION_CATEGORY,
            Author::PERMISSION_CATEGORY,
        ];
        $actions = [
            UserAction::READ,
            UserAction::UPDATE,
            UserAction::DELETE,
            UserAction::CREATE,
        ];

        $permissionNameFactory = Yii::$container->get(PermissionNameFactoryInterface::class);
        foreach ($categories as $category) {
            foreach ($actions as $action) {
                $permissionName = $permissionNameFactory->getName($category, $action);
                $permission = $auth->createPermission($permissionName);
                $auth->add($permission);
                $auth->addChild($userRole, $permission);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();
    }
}
