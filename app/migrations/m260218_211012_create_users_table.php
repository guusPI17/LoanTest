<?php

declare(strict_types=1);

namespace app\migrations;

use yii\db\Migration;

/**
 * Создаёт таблицу пользователей.
 */
class m260218_211012_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
        ]);

        $this->execute("COMMENT ON TABLE users IS 'Пользователи'");
        $this->execute("COMMENT ON COLUMN users.id IS 'Уникальный идентификатор пользователя'");
        $this->execute("COMMENT ON COLUMN users.name IS 'Имя пользователя'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%users}}');
    }
}
