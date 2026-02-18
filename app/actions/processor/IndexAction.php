<?php

declare(strict_types=1);

namespace app\actions\processor;

use app\enums\LoanRequestStatusCode;
use app\models\LoanRequest;
use Yii;
use yii\base\Action;

/**
 * Action обработки заявок на займ (GET /processor).
 *
 * Запускает процесс принятия решений по всем необработанным заявкам (status IS NULL).
 * Заявки разных пользователей и заявки одного пользователя обрабатываются параллельно.
 * Корректность обеспечивается двумя уровнями mutex-блокировок (PgsqlMutex):
 *  - блокировка по ключу заявки исключает двойную обработку одной заявки;
 *  - блокировка по ключу пользователя, удерживаемая только в момент одобрения,
 *    исключает двойное одобрение пользователя.
 */
class IndexAction extends Action
{
    /**
     * Вероятность одобрения заявки (в процентах).
     */
    private const APPROVAL_PROBABILITY = 10;

    /**
     * Максимальная задержка обработки (секунды).
     */
    private const MAX_DELAY = 300;

    /**
     * Таймаут ожидания mutex-блокировки пользователя (секунды).
     */
    private const MUTEX_TIMEOUT = 300;

    /**
     * Запуск обработки заявок на займ.
     *
     * @return array Результат выполнения операции
     */
    public function run(): array
    {
        $delay = min(max(0, (int) Yii::$app->request->get('delay', 0)), self::MAX_DELAY);

        $pendingIds = LoanRequest::find()
            ->select('id')
            ->where(['status' => null])
            ->column();

        foreach ($pendingIds as $id) {
            $this->processRequest((int) $id, $delay);
        }

        return ['result' => true];
    }

    /**
     * Обрабатывает одну заявку.
     *
     * Mutex по ключу заявки позволяет параллельно обрабатывать любые заявки,
     * в том числе заявки одного пользователя.
     *
     * @param int $id Идентификатор заявки
     * @param int $delay Задержка принятия решения в секундах
     */
    private function processRequest(int $id, int $delay): void
    {
        $mutex = Yii::$app->mutex;
        $requestKey = 'loan_request:' . $id;

        // Acquire без ожидания — если заявка уже обрабатывается, пропускаем.
        if (!$mutex->acquire($requestKey)) {
            return;
        }

        try {
            $request = LoanRequest::findOne($id);

            // Перепроверяем статус: другой процесс мог завершить обработку
            // пока мы получали список ID выше.
            if ($request === null || $request->status !== null) {
                return;
            }

            // Эмуляция длительного принятия решения.
            sleep($delay);

            if (random_int(1, 100) <= self::APPROVAL_PROBABILITY) {
                $this->approveRequest($request);
            } else {
                $this->setStatus($request, LoanRequestStatusCode::Declined->value);
            }
        } finally {
            $mutex->release($requestKey);
        }
    }

    /**
     * Одобряет заявку с защитой от двойного одобрения пользователя.
     *
     * Mutex по ключу пользователя удерживается только на время проверки
     * и обновления статуса — sleep() к этому моменту уже завершён, поэтому
     * параллельная обработка других заявок того же пользователя не блокируется.
     *
     * @param LoanRequest $request Заявка на одобрение
     */
    private function approveRequest(LoanRequest $request): void
    {
        $mutex = Yii::$app->mutex;
        $userKey = 'loan_user:' . $request->user_id;

        // Acquire с ожиданием — блокируемся до освобождения ключа пользователя.
        if (!$mutex->acquire($userKey, self::MUTEX_TIMEOUT)) {
            return;
        }

        try {
            $hasApproved = LoanRequest::find()
                ->where([
                    'user_id' => $request->user_id,
                    'status' => LoanRequestStatusCode::Approved->value,
                ])
                ->exists();

            $this->setStatus(
                $request,
                $hasApproved
                    ? LoanRequestStatusCode::Declined->value
                    : LoanRequestStatusCode::Approved->value
            );
        } finally {
            $mutex->release($userKey);
        }
    }

    /**
     * Устанавливает статус заявки.
     *
     * @param LoanRequest $request Заявка
     * @param string $status Новый статус
     */
    private function setStatus(LoanRequest $request, string $status): void
    {
        $request->status = $status;

        if (!$request->save()) {
            Yii::error(
                sprintf(
                    'Failed to set status "%s" for loan request #%d: %s',
                    $status,
                    $request->id,
                    json_encode($request->getErrors())
                ),
                __CLASS__
            );
        }
    }
}
