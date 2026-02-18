<?php

declare(strict_types=1);

namespace app\migrations;

use yii\db\Migration;

/**
 * Создаёт справочник статусов заявок на займ и наполняет его начальными данными.
 */
class m260218_211345_create_loan_request_statuses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%loan_request_statuses}}', [
            'code' => $this->string(20)->notNull()->append('PRIMARY KEY'),
            'name' => $this->string(100)->notNull(),
        ]);

        $this->execute("COMMENT ON TABLE loan_request_statuses IS 'Справочник статусов заявок на займ'");
        $this->execute("COMMENT ON COLUMN loan_request_statuses.code IS 'Код статуса (первичный ключ)'");
        $this->execute("COMMENT ON COLUMN loan_request_statuses.name IS 'Название статуса'");

        $this->batchInsert('{{%loan_request_statuses}}', ['code', 'name'], [
            ['approved', 'Одобрена'],
            ['declined', 'Отклонена'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%loan_request_statuses}}');
    }
}
