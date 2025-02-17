<?php

namespace app\forms;

use app\models\Book;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * @property int $id
 * @property string $title
 * @property int|null $year
 * @property string|null $description
 * @property string $isbn
 * @property int[] $authors
 * @property UploadedFile $cover_img
 */
class EditBookForm extends Model
{
    public ?int $id;
    public ?string $title;
    public ?int $year;
    public ?string $description;
    public ?string $isbn;
    public array $authors = [];
    public ?string $cover_img_path;
    public ?UploadedFile $cover_img = null;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'isbn'], 'required'],
            [['year'], 'integer'],
            [['description'], 'string'],
            [['isbn'], 'string', 'min' => 13, 'max' => 13],
            [['title', 'isbn'], 'string', 'max' => 255],
            [['authors'], 'each', 'rule' => ['integer']],
            [['cover_img'], 'file', 'extensions' => 'png, jpg'],
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
            'authors' => Yii::t('app', 'Authors'),
            'cover_img' => Yii::t('app', 'Cover'),
        ];
    }

    /**
     * Заполнение формы данными из модели
     *
     * @param Book $book
     *
     * @return void
     */
    public function fillFromBook(Book $book): void
    {
        $this->setAttributes($book->getAttributes(), false);
        $this->authors = ArrayHelper::getColumn($book->authors, 'id');
    }
}
