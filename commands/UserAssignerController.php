<?php

namespace app\commands;

use app\models\User;
use Exception;
use Yii;
use yii\console\Controller;

/**
 * Контроллер для назначения юзеров в группы
 */
class UserAssignerController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;

        /**
         * ID берется первое из @see User::$users
         */
        $auth->assign($auth->getRole('user'), '100');
    }
}
