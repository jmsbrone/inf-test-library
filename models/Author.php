<?php

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "authors".
 *
 * @property int $id
 * @property string $name
 * @property string $surname
 * @property string $last_name
 *
 * @property Book[] $books
 * @property Subscription[] $subscriptions
 */
class Author extends ActiveRecord
{
    public const PERMISSION_CATEGORY = 'Author';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%authors}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'surname', 'last_name'], 'required'],
            [['name', 'surname', 'last_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'surname' => Yii::t('app', 'Surname'),
            'last_name' => Yii::t('app', 'Last Name'),
        ];
    }

    /**
     * Gets query for [[Books]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])->viaTable('author_book', ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Subscriptions]].
     *
     * @return ActiveQuery
     */
    public function getSubscriptions()
    {
        return $this->hasMany(Subscription::class, ['author_id' => 'id']);
    }

    /**
     * Получение ФИО автора
     *
     * @param bool $withInitials Отдача ФИО с инициалами вместо полного имени (прим. Иванов И.И.)
     *
     * @return string
     */
    public function getFullName(bool $withInitials = true): string
    {
        if ($withInitials) {
            $secondNamePart= $this->name[0] . '.' . $this->last_name[0] . '.';
        } else {
            $secondNamePart = $this->name . ' ' . $this->last_name;
        }

        return $this->surname . ' ' . $secondNamePart;
    }
}
