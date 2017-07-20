<?php
use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateGeoI18n extends AbstractMigration
{
    public $autoId = false;

    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('geo_i18n');
        $table->addColumn('id', 'integer', [ 'autoIncrement' => true, 'signed' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'null' => false, ])
        ->addColumn('locale', 'string', [ 'limit' => 6, 'null' => false, ])
        ->addColumn('model', 'string', [ 'limit' => 255, 'null' => false, ])
        ->addColumn('foreign_key', 'integer', [ 'signed' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'null' => false ])
        ->addColumn('field', 'string', [ 'limit' => 255, 'null' => false, ])
        ->addColumn('content', 'text', [ 'limit' => MysqlAdapter::TEXT_TINY, 'null' => true, ])
        ->addPrimaryKey([ 'id' ])
        ->addIndex([ 'model', 'foreign_key', 'field' ])
        ->addIndex([ 'locale', 'model', 'foreign_key', 'field' ], ['unique' => true])
        ->create();
    }
}
