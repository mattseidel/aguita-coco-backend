<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'user_type' => $this->integer()->defaultValue(1),
            'phone' => $this->string(10)->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);


        $this->createTable('product', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'description' => $this->text(),
            'price' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('discount', [
            'id' => $this->primaryKey(),
            'productId' => $this->integer(),
            'start_date' => $this->date(),
            'end_date' => $this->date(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_discount_product',
            'discount',
            'productId',
            'product',
            'id'
        );
    }

    public function down()
    {
        $this->removeForeignKey('fk_discount_product');
        $this->dropTable('discount');
        $this->dropTable('product');
        $this->dropTable('user');
    }
}
