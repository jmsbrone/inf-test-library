<?php

namespace app\models;

use app\notifications\NewBookNotifierInterface;
use Exception;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\di\NotInstantiableException;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "books".
 *
 * @property int $id
 * @property string $title
 * @property int|null $year
 * @property string|null $description
 * @property string $isbn
 * @property string $cover_img_path
 *
 * @property Author[] $authors
 */
class Book extends ActiveRecord
{
    public const PERMISSION_CATEGORY = 'Book';

    protected ?UploadedFile $newCover = null;
    protected ?array $newAuthorIds = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%books}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'isbn'], 'required'],
            [['year'], 'integer'],
            [['description', 'cover_img_path'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 13],
            [['isbn'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'year' => Yii::t('app', 'Year'),
            'description' => Yii::t('app', 'Description'),
            'isbn' => Yii::t('app', 'Isbn'),
            'cover_img_path' => Yii::t('app', 'Cover'),
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (isset($this->newCover) && isset($changedAttributes['cover_img_path'])) {
            $oldCoverPath = $this->getFilePathToCover($changedAttributes['cover_img_path']);
            if (file_exists($oldCoverPath)) {
                unlink($oldCoverPath);
            }
            unset($this->newCover);
        }

        if (isset($this->newAuthorIds)) {
            $currentAuthors = ArrayHelper::index($this->authors, 'id');
            $newAuthors = Author::find()->where(['id' => $this->newAuthorIds])->all();
            $newAuthors = ArrayHelper::index($newAuthors, 'id');

            foreach($newAuthors as $newAuthor) {
                if (!isset($currentAuthors[$newAuthor->id])) {
                    $this->link('authors', $newAuthor);
                } else {
                    unset($currentAuthors[$newAuthor->id]);
                }
            }

            foreach($currentAuthors as $currentAuthor) {
                $this->unlink('authors', $currentAuthor, true);
            }

            unset($this->newAuthorIds);
        }

        if (!$insert) {
            return;
        }

        if (Yii::$container->has(NewBookNotifierInterface::class)) {
            $this->sendNewBookNotifications();
        }
    }

    public function beforeSave($insert)
    {
        $parentResult = parent::beforeSave($insert);
        if (!$parentResult) {
            return $parentResult;
        }

        if (isset($this->newCover)) {
            $this->cover_img_path = '/uploads/' . $this->newCover->baseName . '.' . $this->newCover->extension;
            $filePathToCover = $this->getFilePathToCover();
            if (!$this->newCover->saveAs($filePathToCover)) {
                throw new Exception('Error while saving cover');
            }
        }

        return $parentResult;
    }

    /**
     * Выставление файла новой обложки.
     *
     * @param UploadedFile $cover
     *
     * @return void Путь к загруженному файлу
     */
    public function setCover(UploadedFile $cover): void
    {
        $this->newCover = $cover;
    }

    /**
     * Установка новых авторов.
     *
     * @param array $ids
     *
     * @return void
     */
    public function setNewAuthors(array $ids): void
    {
        $this->newAuthorIds = $ids;
    }

    /**
     * Gets query for [[Authors]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])->viaTable('{{%author_book}}', ['book_id' => 'id']);
    }

    /**
     * Отправка уведомлений о выходе книги.
     *
     * @return void
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function sendNewBookNotifications(): void
    {
        $notifier = Yii::$container->get(NewBookNotifierInterface::class);

        $authors = $this->authors;
        foreach ($authors as $author) {
            foreach ($author->subscriptions as $subscription) {
                try {
                    $notifier->notify($this, $subscription);
                } catch (Throwable) {
                    // ошибки отправки уведомления игнорируем
                }
            }
        }
    }

    /**
     * Возвращает путь на диске к текущему файлу обложки.
     *
     * @param string|null $relativeCoverPath
     *
     * @return string|null
     */
    public function getFilePathToCover(?string $relativeCoverPath = null): string|null
    {
        if (!isset($relativeCoverPath)) {
            $relativeCoverPath = $this->cover_img_path;
        }
        if (empty($relativeCoverPath)) {
            $result = null;
        } else {
            $result = Yii::getAlias('@app/web') . $relativeCoverPath;
        }

        return $result;
    }
}
