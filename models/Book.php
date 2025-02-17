<?php

namespace app\models;

use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
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

    public function uploadCover(UploadedFile $cover): string
    {
        $this->cover_img_path = '/uploads/' . $cover->baseName . '.' . $cover->extension;
        $filePathToCover = '@app/web' . $this->cover_img_path;
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
}
