<?php

declare(strict_types=1);

namespace app\migrations;

use yii\db\Migration;

/**
 * Создаёт таблицу заявок на займ.
 */
class m260218_211841_create_loan_requests_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%loan_requests}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'amount' => $this->integer()->notNull(),
            'term' => $this->integer()->notNull(),
            'status' => $this->string(20)->null()->defaultValue(null),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('NOW()'),
        ]);

        $this->addForeignKey(
            'fk_loan_requests_user_id',
            '{{%loan_requests}}',
            'user_id',
            '{{%users}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_loan_requests_status',
            '{{%loan_requests}}',
            'status',
            '{{%loan_request_statuses}}',
            'code',
            'RESTRICT',
            'CASCADE'
        );

        $this->createIndex('idx_loan_requests_user_id', '{{%loan_requests}}', 'user_id');
        $this->createIndex('idx_loan_requests_status', '{{%loan_requests}}', 'status');

        $this->execute("COMMENT ON TABLE loan_requests IS 'Заявки на займ'");
        $this->execute("COMMENT ON COLUMN loan_requests.id IS 'Уникальный идентификатор заявки'");
        $this->execute("COMMENT ON COLUMN loan_requests.user_id IS 'Идентификатор пользователя, подавшего заявку'");
        $this->execute("COMMENT ON COLUMN loan_requests.amount IS 'Запрашиваемая сумма займа'");
        $this->execute("COMMENT ON COLUMN loan_requests.term IS 'Срок займа в днях'");
        $this->execute("COMMENT ON COLUMN loan_requests.status IS 'Статус заявки: null — ожидает обработки, approved — одобрена, declined — отклонена'");
        $this->execute("COMMENT ON COLUMN loan_requests.created_at IS 'Дата и время создания заявки'");
        $this->execute("COMMENT ON COLUMN loan_requests.updated_at IS 'Дата и время последнего обновления заявки'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropForeignKey('fk_loan_requests_user_id', '{{%loan_requests}}');
        $this->dropForeignKey('fk_loan_requests_status', '{{%loan_requests}}');
        $this->dropTable('{{%loan_requests}}');
    }
}
