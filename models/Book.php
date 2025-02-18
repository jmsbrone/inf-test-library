<?php

namespace app\models;

use app\notifications\NewBookNotifierInterface;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\di\NotInstantiableException;
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

        if (!$insert) {
            return;
        }

        if (Yii::$container->has(NewBookNotifierInterface::class)) {
            $this->sendNewBookNotifications();
        }
    }

    /**
     * Загрузка файла обложки с сохранением на диске.
     *
     * @param UploadedFile $cover
     *
     * @return string Путь к загруженному файлу
     * @throws Exception
     */
    public function uploadCover(UploadedFile $cover): string
    {
        $this->cover_img_path = '/uploads/' . $cover->baseName . '.' . $cover->extension;
        $filePathToCover = $this->getFilePathToCover();
        if (!$cover->saveAs($filePathToCover)) {
            throw new Exception('Error while saving cover');
        }

        return $filePathToCover;
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
                $notifier->notify($this, $subscription);
            }
        }
    }

    /**
     * Возвращает путь на диске к текущему файлу обложки.
     *
     * @return string|null
     */
    public function getFilePathToCover(): string|null
    {
        if (empty($this->cover_img_path)) {
            $result = null;
        } else {
            $result = Yii::getAlias('@app/web') . $this->cover_img_path;
        }

        return $result;
    }
}
