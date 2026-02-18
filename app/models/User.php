<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Модель пользователя.
 *
 * @property int $id Уникальный идентификатор пользователя
 * @property string $name Имя пользователя
 * @property LoanRequest[] $loanRequests Заявки на займ пользователя
 */
class User extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
        ];
    }

    /**
     * Возвращает заявки на займ пользователя.
     */
    public function getLoanRequests(): ActiveQuery
    {
        return $this->hasMany(LoanRequest::class, ['user_id' => 'id']);
    }
}
