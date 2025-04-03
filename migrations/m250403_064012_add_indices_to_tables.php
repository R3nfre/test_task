<?php

namespace migrations;

use yii\db\Migration;

class m250403_064012_add_indices_to_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Индексы для таблицы orders
        $this->createIndex('idx_orders_user_id', 'orders', 'user_id');
        $this->createIndex('idx_orders_service_id', 'orders', 'service_id');
        $this->createIndex('idx_orders_status', 'orders', 'status');
        $this->createIndex('idx_orders_mode', 'orders', 'mode');
        $this->createIndex('idx_orders_created_at', 'orders', 'created_at');

        // Комбинированные индексы для частых сценариев фильтрации
        $this->createIndex('idx_orders_status_service_id', 'orders', ['status', 'service_id']);
        $this->createIndex('idx_orders_status_mode', 'orders', ['status', 'mode']);
        $this->createIndex('idx_orders_service_id_mode', 'orders', ['service_id', 'mode']);
        $this->createIndex('idx_orders_status_service_id_mode', 'orders', ['status', 'service_id', 'mode']);

        // Индексы для таблицы users (для поиска по имени)
        $this->createIndex('idx_users_first_name', 'users', 'first_name');
        $this->createIndex('idx_users_last_name', 'users', 'last_name');
        $this->createIndex('idx_users_full_name', 'users', ['first_name', 'last_name']);

        // Индекс для таблицы services (для поиска по имени сервиса)
        $this->createIndex('idx_services_name', 'services', 'name');

        // Добавление внешних ключей для связей между таблицами
        $this->addForeignKey('fk_orders_user_id', 'orders', 'user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_orders_service_id', 'orders', 'service_id', 'services', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Удаление внешних ключей
        $this->dropForeignKey('fk_orders_service_id', 'orders');
        $this->dropForeignKey('fk_orders_user_id', 'orders');

        // Удаление индексов таблицы orders
        $this->dropIndex('idx_orders_user_id', 'orders');
        $this->dropIndex('idx_orders_service_id', 'orders');
        $this->dropIndex('idx_orders_status', 'orders');
        $this->dropIndex('idx_orders_mode', 'orders');
        $this->dropIndex('idx_orders_created_at', 'orders');
        $this->dropIndex('idx_orders_status_service_id', 'orders');
        $this->dropIndex('idx_orders_status_mode', 'orders');
        $this->dropIndex('idx_orders_service_id_mode', 'orders');
        $this->dropIndex('idx_orders_status_service_id_mode', 'orders');

        // Удаление индексов таблицы users
        $this->dropIndex('idx_users_first_name', 'users');
        $this->dropIndex('idx_users_last_name', 'users');
        $this->dropIndex('idx_users_full_name', 'users');

        // Удаление индекса таблицы services
        $this->dropIndex('idx_services_name', 'services');
    }
}
