<?php
namespace Geo\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class CountriesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('geo_countries');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        // Translate Behavior
        $this->addBehavior('Translate', ['translationTable' => 'Geo.I18n', 'fields' => ['title']]);
    }
}
