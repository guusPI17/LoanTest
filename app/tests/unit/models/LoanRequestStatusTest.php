<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\enums\LoanRequestStatusCode;
use app\models\LoanRequestStatus;
use app\tests\TestCase;

/**
 * Тесты модели справочника статусов заявок.
 */
class LoanRequestStatusTest extends TestCase
{
    /**
     * Проверяет, что первичный ключ модели — строковый код статуса.
     */
    public function testPrimaryKeyIsCode(): void
    {
        $this->assertSame(['code'], LoanRequestStatus::primaryKey());
    }

    /**
     * Проверяет, что все статусы из Enum присутствуют в базе данных.
     */
    public function testAllEnumStatusesExistInDatabase(): void
    {
        foreach (LoanRequestStatusCode::cases() as $status) {
            $model = LoanRequestStatus::findOne($status->value);
            $this->assertNotNull($model, "Status '{$status->value}' must exist in the database.");
            $this->assertSame($status->value, $model->code);
        }
    }

    /**
     * Проверяет, что валидация проходит для корректных данных.
     *
     * Тестируем только поле name: все допустимые code уже есть в БД (сид),
     * поэтому правило unique на code здесь не применяется.
     */
    public function testValidationPassesForValidData(): void
    {
        $status = new LoanRequestStatus();
        $status->code = LoanRequestStatusCode::Approved->value;
        $status->name = 'Одобрена';

        $this->assertTrue($status->validate(['name']));
        $this->assertFalse($status->hasErrors('name'));
    }

    /**
     * Проверяет, что валидация не проходит при отсутствии обязательных полей.
     */
    public function testValidationFailsWhenRequiredFieldsMissing(): void
    {
        $status = new LoanRequestStatus();

        $this->assertFalse($status->validate());
        $this->assertTrue($status->hasErrors('code'));
        $this->assertTrue($status->hasErrors('name'));
    }

    /**
     * Проверяет, что валидация не проходит для кода, отсутствующего в Enum.
     */
    public function testValidationFailsForCodeNotInEnum(): void
    {
        $status = new LoanRequestStatus();
        $status->code = 'pending';
        $status->name = 'Ожидает';

        $this->assertFalse($status->validate(['code']));
        $this->assertTrue($status->hasErrors('code'));
    }
}
