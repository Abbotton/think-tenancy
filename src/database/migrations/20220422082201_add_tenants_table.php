<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

use think\migration\db\Column;
use think\migration\Migrator;

class AddTenantsTable extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table(
            'tenants',
            ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci']
        )->setComment('租户表');
        $table->setPrimaryKey('id');
        $table->addColumn(Column::string('name'))->setComment('租户名称');
        $table->addColumn(Column::string('sub_domain', 128))->setComment('子域名');
        $table->addColumn(Column::integer('expired_at'))->setComment('过期时间');
        $table->addColumn(Column::string('db_name'))->setComment('租户数据库名称');
        $table->addColumn(Column::string('db_username'))->setComment('租户数据库用户名');
        $table->addColumn(Column::string('db_password'))->setComment('租户数据库密码');
        $table->addColumn(Column::boolean('status'))->setComment('租户状态');
        $table->addIndex('sub_domain');
        $table->addIndex('expired_at');
        $table->addIndex('db_name');
        $table->addIndex('db_username');
        $table->addIndex('db_password');
        $table->addTimestamps();
        $table->addSoftDelete();
        $table->create();
    }
}
