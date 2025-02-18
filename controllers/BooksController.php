<?php

namespace app\controllers;

use app\auth\PermissionNameFactoryInterface;
use app\forms\EditBookForm;
use app\models\Author;
use app\models\Book;
use app\values\UserAction;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\UploadedFile;

class BooksController extends Controller
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
        $createBookPermission = $this->getCreatePermissionName();
        $updateBookPermission = $this->getUpdatePermissionName();
        $deleteBookPermission = $this->getDeletePermissionName();

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
                        'actions' => ['create'],
                        'roles' => [$createBookPermission],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => [$updateBookPermission],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => [$deleteBookPermission],
                    ],
                ],
            ],
        ];
    }

    public function actionList()
    {
        $bookQuery = Book::find();
        $author_id = $this->request->get('author');
        if (!empty($author_id)) {
            $bookQuery->joinWith('authors')->where(['{{%authors}}.id' => $author_id]);
        }

        $books = $bookQuery->all();

        return $this->render('list', ['books' => $books]);
    }

    public function actionCreate()
    {
        $book = new Book();
        if ($this->request->getIsPost()) {
            $this->updateFromPost($book);
            return $this->redirect(Url::to(['books/view', 'id' => $book->id]));
        }

        return $this->render('detail', [
            'book' => $book,
            'enableEdit' => true,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $book = $this->getBookById($id);

        $this->updateFromPost($book);

        return $this->redirect(Url::to(['books/view', 'id' => $book->id]));
    }

    public function actionView(int $id)
    {
        $book = $this->getBookById($id);

        return $this->render('detail', [
            'book' => $book,
            'enableEdit' => Yii::$app->user->can($this->getUpdatePermissionName()),
        ]);
    }

    public function actionDelete(int $id)
    {
        $book = $this->getBookById($id);

        $book->delete();

        return $this->redirect(Url::to(['books/list']));
    }

    /**
     * @param Book $book
     *
     * @return void
     * @throws UserException
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function updateFromPost(Book $book): void
    {
        $editForm = new EditBookForm();
        $editFormName = $editForm->formName();

        $coverImgFile = UploadedFile::getInstance($editForm, 'cover_img');
        if ($coverImgFile !== null) {
            $savedCoverPath = $book->uploadCover($coverImgFile);
        }

        $data = $this->request->post();
        $loaded = $book->load($data, $editFormName);
        if (!$loaded || !$book->validate()) {
            throw new UserException('Invalid data: ' . json_encode($book->errors));
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$book->save()) {
                throw new Exception('Could not save book data: ' . json_encode($book->errors));
            }

            foreach ($book->authors as $author) {
                $book->unlink('authors', $author, true);
            }

            $newAuthors = Author::find()->where(['id' => $data[$editFormName]['authors']])->all();
            foreach ($newAuthors as $author) {
                $book->link('authors', $author);
            }

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();

            if (isset($savedCoverPath)) {
                unlink($savedCoverPath);
            }

            throw $e;
        }
    }

    /**
     * @param int $id
     *
     * @return Book
     * @throws UserException
     */
    public function getBookById(int $id): Book
    {
        $book = Book::findOne($id);
        if (!$book) {
            throw new UserException('Book not found');
        }
        return $book;
    }

    /**
     * @return string
     */
    public function getUpdatePermissionName(): string
    {
        return $this->permissionNameFactory->getName(
            Book::PERMISSION_CATEGORY, UserAction::UPDATE,
        );
    }

    /**
     * @return string
     */
    public function getCreatePermissionName(): string
    {
        return $this->permissionNameFactory->getName(
            Book::PERMISSION_CATEGORY, UserAction::CREATE,
        );
    }

    /**
     * @return string
     */
    public function getDeletePermissionName(): string
    {
        return $this->permissionNameFactory->getName(
            Book::PERMISSION_CATEGORY, UserAction::DELETE,
        );
    }
}
