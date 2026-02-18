<?php

declare(strict_types=1);

namespace app\controllers;

use app\actions\processor\IndexAction;
use yii\web\Controller;

/**
 * Контроллер обработки заявок на займ.
 *
 * Запускает процесс принятия решений по всем ожидающим заявкам.
 */
class ProcessorController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'index' => IndexAction::class,
        ];
    }
}
