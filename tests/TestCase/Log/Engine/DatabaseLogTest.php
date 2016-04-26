<?php
namespace Logging\Test\TestCase\Log\Engine;

use Logging\Log\Engine\DatabaseLog;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class DatabaseLogTest extends TestCase {

    public $DatabaseLog;

    public $fixtures = [
        'plugin.Logging.Logs',
    ];

    public function setUp() {
        parent::setUp();

        $this->DatabaseLog = new DatabaseLog();
    }

    public function tearDown() {
        unset($this->DatabaseLog);
        $_SESSION = [];

        parent::tearDown();
    }

    /**
        }====> log() <====={
     */
        public function testLogNotLoggedAndNoSession() {

            unset($_SESSION);

            $this->assertTrue( $this->DatabaseLog->log('info', 'My message', ['scope' => ['myScope']]) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('info', $result->level);
            $this->assertEquals('myScope', $result->scope);
            $this->assertEquals('My message', $result->message);
            $this->assertNull($result->user_id);
            $this->assertEmpty($result->context);
        }

        public function testLogNotLogged() {

            $this->assertTrue( $this->DatabaseLog->log('info', 'My message', ['scope' => ['myScope']]) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('info', $result->level);
            $this->assertEquals('myScope', $result->scope);
            $this->assertEquals('My message', $result->message);
            $this->assertNull($result->user_id);
            $this->assertEmpty($result->context);
        }

        public function testLogLogged() {

            $_SESSION = [
                'Auth' => [
                    'User' => [
                        'id' => 1,
                    ]
                ]
            ];

            $this->assertTrue( $this->DatabaseLog->log('info', 'My message', ['scope' => ['myScope']]) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('info', $result->level);
            $this->assertEquals('myScope', $result->scope);
            $this->assertEquals('My message', $result->message);
            $this->assertEquals(1, $result->user_id);
            $this->assertEmpty($result->context);
        }

        public function testLogChangeUserId() {

            $_SESSION = [
                'Auth' => [
                    'User' => [
                        'id' => 1,
                    ]
                ]
            ];

            $this->assertTrue( $this->DatabaseLog->log('info', 'My message', ['scope' => ['myScope'], 'userId' => 3]) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals(3, $result->user_id);
            $this->assertEmpty($result->context);
        }

        public function testLogDontSaveUserId() {

            $_SESSION = [
                'Auth' => [
                    'User' => [
                        'id' => 1,
                    ]
                ]
            ];

            $this->assertTrue( $this->DatabaseLog->log('info', 'My message', ['scope' => ['myScope'], 'userId' => null]) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertNull($result->user_id);
            $this->assertEmpty($result->context);
        }

        public function testLogAddContext() {

            $this->assertTrue( $this->DatabaseLog->log('info', 'My message', ['scope' => ['myScope'], 'pull' => 'request']) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('request', $result->context['pull']);
        }

        public function testLogIfScopeIsRequiredReturnFalseIfNotPassedScope() {

            $this->DatabaseLog = new DatabaseLog(['requiredScope' => true]);
            $this->assertFalse( $this->DatabaseLog->log('info', 'My message') );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEmpty($result);
        }

        public function testLogIfScopeIsRequiredReturnFalseIfEmptyScope() {

            $this->DatabaseLog = new DatabaseLog(['requiredScope' => true]);
            $this->assertFalse( $this->DatabaseLog->log('info', 'My message', ['scope' => []]) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEmpty($result);
        }

        public function testLogIfScopeIsRequiredReturnTrueIfNotEmptyScope() {

            $this->DatabaseLog = new DatabaseLog(['requiredScope' => true]);
            $this->assertTrue( $this->DatabaseLog->log('info', 'My message', ['scope' => ['myScope']]) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('myScope', $result->scope);
        }

        public function testLogIfScopeIsNotRequiredReturnTrueIfNotPassedScope() {

            $this->DatabaseLog = new DatabaseLog(['requiredScope' => false]);
            $this->assertTrue( $this->DatabaseLog->log('info', 'My message') );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('My message', $result->message);
            $this->assertNull($result->scope);
        }

        public function testLogIfScopeIsNotRequiredReturnTrueIfEmptyScope() {

            $this->DatabaseLog = new DatabaseLog(['requiredScope' => false]);
            $this->assertTrue( $this->DatabaseLog->log('info', 'My message', ['scope' => []]) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('My message', $result->message);
            $this->assertNull($result->scope);
        }

        public function testLogIfScopeIsNotRequiredReturnTrueIfNotEmptyScope() {

            $this->DatabaseLog = new DatabaseLog(['requiredScope' => false]);
            $this->assertTrue( $this->DatabaseLog->log('info', 'My message', ['scope' => ['myScope']]) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('myScope', $result->scope);
        }
}