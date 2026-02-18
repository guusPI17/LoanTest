<?php

declare(strict_types=1);

namespace app\actions\requests;

use app\models\LoanRequest;
use Yii;
use yii\base\Action;

/**
 * Action подачи новой заявки на займ (POST /requests).
 *
 * Принимает JSON-тело запроса с полями user_id, amount, term.
 * Сохраняет заявку в базе данных (status=null — ожидает обработки).
 */
class CreateAction extends Action
{
    /**
     * Подача новой заявки на займ.
     *
     * @return array Результат операции с идентификатором созданной заявки
     *               либо признак неуспеха при ошибке валидации
     */
    public function run(): array
    {
        $model = new LoanRequest();
        $model->load(Yii::$app->request->getBodyParams(), '');

        if (!$model->validate() || !$model->save(false)) {
            Yii::$app->response->statusCode = 400;

            return ['result' => false];
        }

        Yii::$app->response->statusCode = 201;

        return [
            'result' => true,
            'id' => $model->id,
        ];
    }
}
