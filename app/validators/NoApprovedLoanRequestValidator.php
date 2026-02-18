<?php

declare(strict_types=1);

namespace app\validators;

use app\enums\LoanRequestStatusCode;
use app\models\LoanRequest;
use yii\validators\Validator;

/**
 * Проверяет, что у пользователя нет одобренной заявки на займ.
 *
 * Валидация выполняется только при создании новой записи.
 * При обновлении существующей записи (например, в процессоре) валидатор пропускается.
 */
class NoApprovedLoanRequestValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function validateAttribute($model, $attribute): void
    {
        if (!$model->isNewRecord) {
            return;
        }

        $exists = LoanRequest::find()
            ->where([
                'user_id' => $model->{$attribute},
                'status' => LoanRequestStatusCode::Approved->value,
            ])
            ->exists();

        if ($exists) {
            $this->addError($model, $attribute, 'User already has an approved loan request.');
        }
    }
}