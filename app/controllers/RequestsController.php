<?php

declare(strict_types=1);

namespace app\controllers;

use app\actions\requests\CreateAction;
use yii\web\Controller;

/**
 * Контроллер для работы с заявками на займ.
 *
 * Обрабатывает подачу новых заявок.
 */
class RequestsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'create' => CreateAction::class,
        ];
    }
}
