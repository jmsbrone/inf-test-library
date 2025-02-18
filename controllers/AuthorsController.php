<?php

namespace app\controllers;

use app\auth\PermissionNameFactoryInterface;
use app\models\Author;
use app\models\Subscription;
use app\values\UserAction;
use Throwable;
use Yii;
use yii\base\UserException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Контроллер для CRUD операций с авторами.
 */
class AuthorsController extends Controller
{
    public function __construct(
        $id,
        $module,
        protected PermissionNameFactoryInterface $permissionNameFactory,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        $updateAuthorPermission = $this->getUpdatePermissionName();
        $createAuthorPermission = $this->getCreatePermissionName();
        $deleteAuthorPermission = $this->getDeletePermissionName();

        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'view', 'subscribe'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => [$updateAuthorPermission],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => [$createAuthorPermission],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => [$deleteAuthorPermission],
                    ],
                ],
            ],
        ];
    }

    /**
     * Просмотр данных автора.
     *
     * @param int $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $author = $this->getAuthorById($id);

        return $this->render('detail', [
            'author' => $author,
            'enableEdit' => Yii::$app->user->can($this->getUpdatePermissionName()),
        ]);
    }

    /**
     * Создание автора.
     *
     * @return string|Response
     * @throws Exception
     * @throws UserException
     */
    public function actionCreate()
    {
        $author = new Author();
        if ($this->request->getIsPost()) {
            $this->updateAuthorFromPost($author);
            return $this->redirect(Url::to(['authors/view', 'id' => $author->id]));
        }

        return $this->render('detail', [
            'author' => $author,
            'enableEdit' => true,
        ]);
    }

    /**
     * Удаление автора.
     *
     * @param int $id
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete(int $id)
    {
        $author = $this->getAuthorById($id);

        $author->delete();

        return $this->redirect(Url::to(['authors/list']));
    }

    /**
     * Обновление данных автора.
     *
     * @param int $id
     *
     * @return Response
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws UserException
     */
    public function actionUpdate(int $id)
    {
        $author = $this->getAuthorById($id);

        $this->updateAuthorFromPost($author);

        return $this->redirect(Url::to(['authors/view', 'id' => $author->id]));
    }

    /**
     * Вывод списка авторов.
     *
     * @return string
     */
    public function actionList(): string
    {
        $authors = Author::find()->all();

        return $this->render('list', ['authors' => $authors]);
    }

    /**
     * Подписка пользователя на автора.
     *
     * @return Response
     * @throws Exception
     * @throws UserException
     */
    public function actionSubscribe()
    {
        $subscription = new Subscription();
        $loaded = $subscription->load($this->request->post(), '');
        if (!$loaded || !$subscription->validate()) {
            throw new UserException('Invalid data: ' . json_encode($subscription->errors));
        }

        $subscriptionExists = Subscription::find()
            ->where([
                'author_id' => $subscription->author_id,
                'phone_number' => $subscription->phone_number,
            ])
            ->exists();

        if ($subscriptionExists) {
            throw new UserException('Already subscribed');
        } elseif (!$subscription->save()) {
            throw new \Exception('Could not save subscription: ' . json_encode($subscription->errors));
        }

        return $this->redirect(Url::to(['authors/list']));
    }

    /**
     * @param Author $author
     *
     * @return void
     * @throws UserException
     * @throws Exception
     * @throws \Exception
     */
    protected function updateAuthorFromPost(Author $author): void
    {
        $loaded = $author->load($this->request->post());
        if (!$loaded || !$author->validate()) {
            throw new UserException('Invalid data: ' . json_encode($author->errors));
        }

        if (!$author->save()) {
            throw new \Exception('Could not save author: ' . json_encode($author->errors));
        }
    }

    /**
     * @param int $id
     *
     * @return Author
     * @throws NotFoundHttpException
     */
    protected function getAuthorById(int $id): Author
    {
        $author = Author::findOne(['id' => $id]);
        if ($author === null) {
            throw new NotFoundHttpException('Author not found');
        }
        return $author;
    }

    /**
     * @return string
     */
    protected function getUpdatePermissionName(): string
    {
        return $this->permissionNameFactory->getName(
            Author::PERMISSION_CATEGORY,
            UserAction::UPDATE,
        );
    }

    /**
     * @return string
     */
    protected function getCreatePermissionName(): string
    {
        return $this->permissionNameFactory->getName(
            Author::PERMISSION_CATEGORY,
            UserAction::CREATE,
        );
    }

    /**
     * @return string
     */
    protected function getDeletePermissionName(): string
    {
        return $this->permissionNameFactory->getName(
            Author::PERMISSION_CATEGORY,
            UserAction::DELETE,
        );
    }
}
