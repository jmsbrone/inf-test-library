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

class LibraryController extends Controller
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
                        'actions' => ['authors', 'view-author'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update-author'],
                        'roles' => [$updateAuthorPermission],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create-author'],
                        'roles' => [$createAuthorPermission],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete-author'],
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
    public function actionViewAuthor(int|null $id = null): string
    {
        if ($id !== null) {
            $author = Author::findOne(['id' => $id]);
            if ($author === null) {
                throw new NotFoundHttpException('Author not found');
            }
        } else {
            $author = new Author();
        }

        return $this->render('author', ['author' => $author]);
    }

    public function actionCreateAuthor()
    {
        $author = new Author();
        if ($this->request->getIsPost()) {
            $this->updateAuthorFromPost($author);
            return $this->redirect(Url::to(['library/view-author', 'id' => $author->id]));
        }

        return $this->render('author', ['author' => $author]);
    }

    public function actionDeleteAuthor(int $id)
    {
        $author = Author::findOne(['id' => $id]);
        if ($author === null) {
            throw new NotFoundHttpException('Author not found');
        }

        $author->delete();

        return $this->redirect(Url::to(['library/authors']));
    }

    public function actionUpdateAuthor()
    {
        $id = $this->request->post('id');
        $author = Author::findOne(['id' => $id]);
        if ($author === null) {
            throw new NotFoundHttpException('Author not found');
        }

        $this->updateAuthorFromPost($author);

        return $this->redirect(Url::to(['library/author', 'id' => $author->id]));
    }

    public function actionAuthors(): string
    {
        $authors = Author::find()->all();

        return $this->render('authors', ['authors' => $authors]);
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
