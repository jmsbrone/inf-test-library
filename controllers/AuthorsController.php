<?php

namespace app\controllers;

use app\auth\PermissionNameFactoryInterface;
use app\models\Author;
use app\values\UserAction;
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
        $updateAuthorPermission = $this->permissionNameFactory->getName(
            Author::PERMISSION_CATEGORY, UserAction::UPDATE,
        );
        $createAuthorPermission = $this->permissionNameFactory->getName(
            Author::PERMISSION_CATEGORY, UserAction::CREATE,
        );
        $deleteAuthorPermission = $this->permissionNameFactory->getName(
            Author::PERMISSION_CATEGORY, UserAction::DELETE,
        );

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
        if ($id !== null) {
            $author = Author::findOne(['id' => $id]);
            if ($author === null) {
                throw new NotFoundHttpException('Author not found');
            }
        } else {
            $author = new Author();
        }

        return $this->render('detail', ['author' => $author]);
    }

    public function actionCreate()
    {
        $author = new Author();
        if ($this->request->getIsPost()) {
            $this->updateAuthorFromPost($author);
            return $this->redirect(Url::to(['authors/view', 'id' => $author->id]));
        }

        return $this->render('detail', ['author' => $author]);
    }

    public function actionDelete(int $id)
    {
        $author = Author::findOne(['id' => $id]);
        if ($author === null) {
            throw new NotFoundHttpException('Author not found');
        }

        $author->delete();

        return $this->redirect(Url::to(['authors/list']));
    }

    public function actionUpdate()
    {
        $id = $this->request->post('id');
        $author = Author::findOne(['id' => $id]);
        if ($author === null) {
            throw new NotFoundHttpException('Author not found');
        }

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
     */
    public function updateAuthorFromPost(Author $author): void
    {
        $loaded = $author->load($this->request->post());
        if (!$loaded || !$author->validate()) {
            throw new UserException('Invalid data: ' . json_encode($author->errors));
        }

        $author->save();
    }
}
