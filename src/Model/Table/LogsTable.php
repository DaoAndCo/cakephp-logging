<?php
namespace Logging\Model\Table;

use Cake\ORM\Table;
use Cake\Database\Schema\Table as Schema;

class LogsTable extends Table {

    public function initialize(array $config) {
        $this->addBehavior('Timestamp');
    }

    protected function _initializeSchema(Schema $schema) {
        $schema->columnType('context', 'loggingJson');
        return $schema;
    }


}