<?php
use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateGeoCountries extends AbstractMigration
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
        $table = $this->table('geo_countries');
        $table->addColumn('id', 'integer', [ 'autoIncrement' => true, 'signed' => false, 'limit' => MysqlAdapter::INT_SMALL, 'null' => false, ])
        ->addColumn('title', 'string', [ 'limit' => 64, 'null' => false, ])
        ->addColumn('alpha2', 'string', [ 'limit' => 2, 'null' => false, ])
        ->addColumn('alpha3', 'string', [ 'limit' => 3, 'null' => false, ])
        ->addColumn('currency', 'string', [ 'limit' => 3, 'null' => false, ])
        ->addColumn('sort', 'integer', [ 'signed' => false, 'limit' => MysqlAdapter::INT_SMALL, 'null' => false ])
        ->addPrimaryKey([ 'id' ])
        ->create();
    }
}
