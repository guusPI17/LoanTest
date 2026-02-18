<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\enums\LoanRequestStatusCode;
use app\models\LoanRequest;
use app\tests\TestCase;

/**
 * Тесты модели заявки на займ.
 */
class LoanRequestTest extends TestCase
{
    /**
     * Проверяет, что валидация проходит для корректных данных.
     */
    public function testValidationPassesForValidData(): void
    {
        $model = $this->buildValidModel();

        $this->assertTrue($model->validate());
        $this->assertFalse($model->hasErrors());
    }

    /**
     * Проверяет, что валидация не проходит при отсутствии обязательных полей.
     */
    public function testValidationFailsWhenRequiredFieldsMissing(): void
    {
        $model = new LoanRequest();

        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('user_id'));
        $this->assertTrue($model->hasErrors('amount'));
        $this->assertTrue($model->hasErrors('term'));
    }

    /**
     * Проверяет, что валидация не проходит при нецелочисленных значениях.
     */
    public function testValidationFailsForNonIntegerValues(): void
    {
        $model = new LoanRequest();
        $model->user_id = 'abc';
        $model->amount = 1.5;
        $model->term = -1;

        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('user_id'));
    }

    /**
     * Проверяет, что валидация не проходит при значениях меньше 1.
     */
    public function testValidationFailsForZeroValues(): void
    {
        $model = new LoanRequest();
        $model->user_id = 0;
        $model->amount = 0;
        $model->term = 0;

        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('user_id'));
        $this->assertTrue($model->hasErrors('amount'));
        $this->assertTrue($model->hasErrors('term'));
    }

    /**
     * Проверяет, что валидация не проходит для недопустимого значения статуса.
     */
    public function testValidationFailsForInvalidStatus(): void
    {
        $userId = $this->createUser();

        $model = $this->buildValidModel($userId);
        $model->status = 'pending';

        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('status'));
    }

    /**
     * Проверяет, что валидация не проходит для несуществующего пользователя.
     */
    public function testValidationFailsForNonExistentUser(): void
    {
        $model = new LoanRequest();
        $model->user_id = 999999;
        $model->amount = 3000;
        $model->term = 30;

        $this->assertFalse($model->validate());
        $this->assertTrue($model->hasErrors('user_id'));
    }

    /**
     * Проверяет, что валидация не проходит, если у пользователя уже есть одобренная заявка.
     */
    public function testValidationFailsWhenUserHasApprovedRequest(): void
    {
        $userId = $this->createUser();

        $approved = $this->buildValidModel($userId);
        $approved->status = LoanRequestStatusCode::Approved->value;
        $approved->save(false);

        $newModel = $this->buildValidModel($userId);

        $this->assertFalse($newModel->validate());
        $this->assertTrue($newModel->hasErrors('user_id'));
    }

    /**
     * Проверяет, что заявку можно успешно сохранить.
     */
    public function testSaveSucceeds(): void
    {
        $model = $this->buildValidModel();

        $this->assertTrue($model->save());
        $this->assertNotNull($model->id);
        $this->assertNull($model->status);
        $this->assertNotNull($model->created_at);
        $this->assertNotNull($model->updated_at);
    }

    /**
     * Строит валидную модель заявки для тестов.
     *
     * @param int|null $userId ID пользователя (если не задан, создаётся новый)
     */
    private function buildValidModel(?int $userId = null): LoanRequest
    {
        $model = new LoanRequest();
        $model->user_id = $userId ?? $this->createUser();
        $model->amount = 3000;
        $model->term = 30;

        return $model;
    }
}
