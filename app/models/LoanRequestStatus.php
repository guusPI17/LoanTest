<?php

declare(strict_types=1);

namespace app\models;

use app\enums\LoanRequestStatusCode;
use yii\db\ActiveRecord;

/**
 * Справочник статусов заявок на займ.
 *
 * Первичный ключ — строковый код статуса.
 * Допустимые коды определяются перечислением {@see LoanRequestStatusCode}.
 *
 * @property string $code Код статуса
 * @property string $name Название статуса
 */
class LoanRequestStatus extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'loan_request_statuses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['code', 'name'], 'required'],
            ['code', 'string', 'max' => 20],
            ['name', 'string', 'max' => 100],
            ['code', 'in', 'range' => array_column(LoanRequestStatusCode::cases(), 'value')],
            ['code', 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'code' => 'Код статуса',
            'name' => 'Название',
        ];
    }
}
