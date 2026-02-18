<?php

declare(strict_types=1);

namespace app\tests\functional;

use app\enums\LoanRequestStatusCode;
use app\models\LoanRequest;
use app\tests\TestCase;
use Yii;

/**
 * Функциональные тесты эндпоинта POST /requests.
 */
class RequestsControllerTest extends TestCase
{
    /**
     * Проверяет успешную подачу заявки: HTTP 201 и корректный JSON-ответ.
     */
    public function testCreateReturns201OnSuccess(): void
    {
        $userId = $this->createUser();

        $response = $this->runAction([
            'user_id' => $userId,
            'amount' => 3000,
            'term' => 30,
        ]);

        $this->assertSame(201, Yii::$app->response->statusCode);
        $this->assertTrue($response['result']);
        $this->assertArrayHasKey('id', $response);
        $this->assertIsInt($response['id']);
    }

    /**
     * Проверяет, что заявка сохраняется в базе данных со статусом null.
     */
    public function testCreateSavesRequestToDatabase(): void
    {
        $userId = $this->createUser();

        $response = $this->runAction([
            'user_id' => $userId,
            'amount' => 5000,
            'term' => 60,
        ]);

        $request = LoanRequest::findOne($response['id']);

        $this->assertNotNull($request);
        $this->assertSame($userId, $request->user_id);
        $this->assertSame(5000, $request->amount);
        $this->assertSame(60, $request->term);
        $this->assertNull($request->status);
    }

    /**
     * Проверяет, что при отсутствии обязательных полей возвращается HTTP 400.
     */
    public function testCreateReturns400WhenFieldsMissing(): void
    {
        $response = $this->runAction([]);

        $this->assertSame(400, Yii::$app->response->statusCode);
        $this->assertFalse($response['result']);
        $this->assertArrayNotHasKey('id', $response);
    }

    /**
     * Проверяет, что при некорректных данных возвращается HTTP 400.
     */
    public function testCreateReturns400WhenDataInvalid(): void
    {
        $response = $this->runAction([
            'user_id' => 0,
            'amount' => -100,
            'term' => 'not_a_number',
        ]);

        $this->assertSame(400, Yii::$app->response->statusCode);
        $this->assertFalse($response['result']);
    }

    /**
     * Проверяет, что нельзя подать заявку, если у пользователя уже есть одобренная.
     */
    public function testCreateReturns400WhenUserHasApprovedRequest(): void
    {
        $userId = $this->createUser();

        $approved = new LoanRequest();
        $approved->user_id = $userId;
        $approved->amount = 1000;
        $approved->term = 15;
        $approved->status = LoanRequestStatusCode::Approved->value;
        $approved->save(false);

        $response = $this->runAction([
            'user_id' => $userId,
            'amount' => 2000,
            'term' => 30,
        ]);

        $this->assertSame(400, Yii::$app->response->statusCode);
        $this->assertFalse($response['result']);
    }

    /**
     * Выполняет действие контроллера с переданными параметрами тела запроса.
     *
     * @param array<string, mixed> $bodyParams Параметры тела запроса
     *
     * @return array<string, mixed> Результат действия контроллера
     */
    private function runAction(array $bodyParams): array
    {
        Yii::$app->request->setBodyParams($bodyParams);

        /** @var array<string, mixed> $result */
        $result = Yii::$app->runAction('requests/create');

        return $result;
    }
}
