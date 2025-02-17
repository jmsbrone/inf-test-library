<?php

namespace app\controllers;

use app\auth\PermissionNameFactoryInterface;
use app\models\Author;
use app\values\UserAction;
use Yii;
use yii\base\UserException;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
                        'actions' => ['list', 'view'],
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
     * @throws NotFoundHttpException
     * @throws UserException
     */
    public function actionView(int|null $id = null): string
    {
        $author = $this->getAuthorById($id);

        return $this->render('detail', [
            'author' => $author,
            'enableEdit' => Yii::$app->user->can($this->getUpdatePermissionName()),
        ]);
    }

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

    public function actionDelete(int $id)
    {
        $author = $this->getAuthorById($id);

        $author->delete();

        return $this->redirect(Url::to(['authors/list']));
    }

    public function actionUpdate(int $id)
    {
        $author = $this->getAuthorById($id);

        $this->updateAuthorFromPost($author);

        return $this->redirect(Url::to(['authors/view', 'id' => $author->id]));
    }

    public function actionList(): string
    {
        $authors = Author::find()->all();

        return $this->render('list', ['authors' => $authors]);
    }

    /**
     * @param Author $author
     *
     * @return void
     * @throws UserException
     * @throws Exception
     * @throws \Exception
     */
    public function updateAuthorFromPost(Author $author): void
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
    public function getAuthorById(int $id): Author
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
    public function getUpdatePermissionName(): string
    {
        return $this->permissionNameFactory->getName(
            Author::PERMISSION_CATEGORY, UserAction::UPDATE,
        );
    }

    /**
     * @return string
     */
    public function getCreatePermissionName(): string
    {
        return $this->permissionNameFactory->getName(
            Author::PERMISSION_CATEGORY, UserAction::CREATE,
        );
    }

    /**
     * @return string
     */
    public function getDeletePermissionName(): string
    {
        return $this->permissionNameFactory->getName(
            Author::PERMISSION_CATEGORY, UserAction::DELETE,
        );
    }
}
