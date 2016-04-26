<?php
namespace Logging\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ContactDatasFixture
 *
 */
class LogsFixture extends TestFixture
{

    public $table = 'logs';

    public $fields = [
          'id' => ['type' => 'integer'],
          'created' => ['type' => 'datetime', 'null' => true],
          'level' => ['type' => 'string', 'length' => 50, 'null' => false],
          'scope' => ['type' => 'string', 'length' => 50, 'null' => true],
          'user_id' => ['type' => 'integer', 'null' => true],
          'message' => ['type' => 'text', 'null' => false],
          'context' => ['type' => 'text', 'null' => false],

          '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
          ]
      ];

    public $records = [

    ];
}
