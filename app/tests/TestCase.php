<?php

declare(strict_types=1);

namespace app\tests;

use app\models\User;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Yii;

/**
 * Базовый класс для всех тестов приложения.
 *
 * Оборачивает каждый тест в транзакцию базы данных, которая откатывается
 * после завершения теста. Это гарантирует изоляцию тестов и чистоту БД.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        Yii::$app->db->beginTransaction();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $transaction = Yii::$app->db->getTransaction();

        if ($transaction !== null) {
            $transaction->rollBack();
        }

        parent::tearDown();
    }

    /**
     * Создаёт пользователя и возвращает его идентификатор.
     *
     * @param string $name Имя пользователя
     */
    protected function createUser(string $name = 'Test User'): int
    {
        $user = new User();
        $user->name = $name;
        $user->save(false);

        return $user->id;
    }
}
