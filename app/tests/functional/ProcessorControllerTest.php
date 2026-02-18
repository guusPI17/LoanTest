<?php

declare(strict_types=1);

namespace app\tests\functional;

use app\enums\LoanRequestStatusCode;
use app\models\LoanRequest;
use app\tests\TestCase;
use Yii;

/**
 * Функциональные тесты эндпоинта GET /processor.
 */
class ProcessorControllerTest extends TestCase
{
    /**
     * Проверяет, что эндпоинт возвращает HTTP 200 и result=true.
     */
    public function testIndexReturnsSuccessResponse(): void
    {
        $response = $this->runAction(0);

        $this->assertSame(200, Yii::$app->response->statusCode);
        $this->assertTrue($response['result']);
    }

    /**
     * Проверяет, что после обработки заявки её статус меняется на approved или declined.
     */
    public function testProcessingChangesRequestStatus(): void
    {
        $requestId = $this->createPendingRequest();

        // Запускаем обработчик несколько раз для гарантированного изменения статуса.
        for ($i = 0; $i < 5; ++$i) {
            $this->runAction(0);

            $request = LoanRequest::findOne($requestId);
            if ($request->status !== null) {
                break;
            }
        }

        $request = LoanRequest::findOne($requestId);
        $this->assertNotNull($request);
        $this->assertContains($request->status, [
            LoanRequestStatusCode::Approved->value,
            LoanRequestStatusCode::Declined->value,
        ]);
    }

    /**
     * Проверяет, что у одного пользователя не более одной одобренной заявки.
     */
    public function testUserCannotHaveMoreThanOneApprovedRequest(): void
    {
        $userId = $this->createUser('Multi Request User');

        for ($i = 0; $i < 5; ++$i) {
            $this->createPendingRequest($userId);
        }

        // Запускаем обработчик многократно, чтобы все заявки были обработаны.
        for ($attempt = 0; $attempt < 20; ++$attempt) {
            $this->runAction(0);

            $pendingCount = LoanRequest::find()
                ->where(['user_id' => $userId, 'status' => null])
                ->count();

            if ($pendingCount === 0) {
                break;
            }
        }

        $approvedCount = LoanRequest::find()
            ->where(['user_id' => $userId, 'status' => LoanRequestStatusCode::Approved->value])
            ->count();

        $this->assertLessThanOrEqual(1, $approvedCount);
    }

    /**
     * Проверяет, что повторный вызов обработчика не меняет уже обработанные заявки.
     */
    public function testAlreadyProcessedRequestsAreNotReprocessed(): void
    {
        $requestId = $this->createPendingRequest();

        $request = LoanRequest::findOne($requestId);
        $request->status = LoanRequestStatusCode::Approved->value;
        $request->save(false);

        $this->runAction(0);

        $request->refresh();
        $this->assertSame(LoanRequestStatusCode::Approved->value, $request->status);
    }

    /**
     * Выполняет действие контроллера с указанной задержкой.
     *
     * @param int $delay Задержка в секундах
     *
     * @return array<string, mixed> Результат действия контроллера
     */
    private function runAction(int $delay): array
    {
        $_GET['delay'] = $delay;

        /** @var array<string, mixed> $result */
        $result = Yii::$app->runAction('processor/index');

        return $result;
    }

    /**
     * Создаёт необработанную заявку (status=null) и возвращает её идентификатор.
     *
     * @param int|null $userId ID пользователя (если не задан, создаётся новый)
     */
    private function createPendingRequest(?int $userId = null): int
    {
        $request = new LoanRequest();
        $request->user_id = $userId ?? $this->createUser();
        $request->amount = 1000;
        $request->term = 30;
        $request->save(false);

        return $request->id;
    }
}
