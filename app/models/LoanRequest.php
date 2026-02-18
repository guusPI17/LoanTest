<?php

declare(strict_types=1);

namespace app\models;

use app\validators\NoApprovedLoanRequestValidator;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Модель заявки на займ.
 *
 * @property int $id Уникальный идентификатор заявки
 * @property int $user_id Идентификатор пользователя, подавшего заявку
 * @property int $amount Запрашиваемая сумма займа
 * @property int $term Срок займа в днях
 * @property string|null $status Текущий статус заявки (null — ожидает обработки)
 * @property string $created_at Дата и время создания заявки
 * @property string $updated_at Дата и время последнего обновления заявки
 * @property User $user Пользователь, подавший заявку
 * @property LoanRequestStatus|null $statusModel Модель статуса заявки
 */
class LoanRequest extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'loan_requests';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'amount', 'term'], 'required'],
            [['user_id', 'amount', 'term'], 'integer'],
            ['user_id', 'number', 'min' => 1],
            ['amount', 'number', 'min' => 1, 'max' => 100000000],
            ['term', 'number', 'min' => 1, 'max' => 3650],
            ['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            [
                'status',
                'exist',
                'targetClass' => LoanRequestStatus::class,
                'targetAttribute' => 'code',
                'skipOnEmpty' => true,
            ],
            ['user_id', NoApprovedLoanRequestValidator::class],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Только user_id, amount и term доступны для массового присвоения.
     */
    public function safeAttributes(): array
    {
        return ['user_id', 'amount', 'term'];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'ID пользователя',
            'amount' => 'Сумма займа',
            'term' => 'Срок займа (дней)',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * Возвращает пользователя, подавшего заявку.
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Возвращает связанную модель статуса заявки.
     */
    public function getStatusModel(): ActiveQuery
    {
        return $this->hasOne(LoanRequestStatus::class, ['code' => 'status']);
    }
}
