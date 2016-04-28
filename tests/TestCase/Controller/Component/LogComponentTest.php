<?php
namespace Logging\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use Logging\Controller\Component\LogComponent;
use Cake\ORM\TableRegistry;
use Cake\Network\Request;
use Cake\Network\Response;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Logging\Controller\Component\LogComponent Test Case
 */
class LogComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Logging\Controller\Component\LogComponent
     */
    public $Log;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.Logging.Logs',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $request = new Request();
        $response = new Response();
        $this->controller = $this->getMock(
            'Cake\Controller\Controller',
            null,
            [$request, $response]
        );
        $registry = new ComponentRegistry($this->controller);
        $this->Log   = new LogComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Log);
        unset($this->LogTable);
        $_SESSION = [];

        parent::tearDown();
    }

    /**
        }====> write() <====={
     */
        public function testWriteNotLoggedNotSession() {

            unset($_SESSION);

            $this->assertTrue( $this->Log->write('info', 'scope', 'Hey man') );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('info', $result->level);
            $this->assertEquals('scope', $result->scope);
            $this->assertEquals('Hey man', $result->message);
            $this->assertNull($result->user_id);
            $this->assertArrayNotHasKey('request', $result->context);
            $this->assertArrayNotHasKey('session', $result->context);
        }

        public function testWriteNotLogged() {

            $this->assertTrue( $this->Log->write('info', 'scope', 'Hey man') );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('info', $result->level);
            $this->assertEquals('scope', $result->scope);
            $this->assertEquals('Hey man', $result->message);
            $this->assertNull($result->user_id);
            $this->assertArrayNotHasKey('request', $result->context);
            $this->assertArrayNotHasKey('session', $result->context);
        }

        public function testWriteLogged() {

            $_SESSION = [
                'Auth' => [
                    'User' => [
                        'id' => 1,
                    ]
                ]
            ];

            $this->assertTrue( $this->Log->write('info', 'scope', 'Hey man') );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('info', $result->level);
            $this->assertEquals('scope', $result->scope);
            $this->assertEquals('Hey man', $result->message);
            $this->assertEquals(1, $result->user_id);
            $this->assertArrayNotHasKey('request', $result->context);
            $this->assertArrayNotHasKey('session', $result->context);
        }

        public function testWriteSaveSession() {

            $this->assertTrue( $this->Log->write('info', 'scope', 'Hey man', [], ['session' => true]) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertArrayNotHasKey('request', $result->context);
            $this->assertArrayHasKey('session', $result->context);
        }

        public function testWriteSaveRequest() {

            $this->assertTrue( $this->Log->write('info', 'scope', 'Hey man', [], ['request' => true]) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertArrayHasKey('request', $result->context);
            $this->assertArrayNotHasKey('session', $result->context);
        }

        public function testWriteAddContext() {

            $this->assertTrue( $this->Log->write('info', 'scope', 'Hey man', ['pull' => 'request']) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('request', $result->context['pull']);
        }

        public function testWriteChangeUserId() {

            $_SESSION = [
                'Auth' => [
                    'User' => [
                        'id' => 1,
                    ]
                ]
            ];

            $this->assertTrue( $this->Log->write('info', 'scope', 'Hey man', ['userId' => 3]) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals(3, $result->user_id);
            $this->assertArrayNotHasKey('userId', $result->context);
        }

        public function testWriteNotSaveUserId() {

            $_SESSION = [
                'Auth' => [
                    'User' => [
                        'id' => 1,
                    ]
                ]
            ];

            $this->assertTrue( $this->Log->write('info', 'scope', 'Hey man', ['userId' => null]) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertNull($result->user_id);
            $this->assertArrayNotHasKey('userId', $result->context);
        }

        public function testWriteSaveIp() {

            $this->assertTrue( $this->Log->write('info', 'scope', 'Hey man', [], ['ip' => true]) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertArrayHasKey('ip', $result->context);
        }

        public function testWriteSaveReferer() {

            $this->assertTrue( $this->Log->write('info', 'scope', 'Hey man', [], ['referer' => true]) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertArrayHasKey('referer', $result->context);
        }

        public function testWriteSaveVars() {

            $this->plugin = 'MyPlugin';
            $registry = new ComponentRegistry($this->controller);
            $this->Log   = new LogComponent($registry, ['vars' => ['plugin' => $this->plugin]]);

            $this->assertTrue( $this->Log->write('info', 'scope', 'Hey man') );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('MyPlugin', $result->context['plugin']);
        }

        public function testWriteOverwriteVars() {

            $this->plugin = 'MyPlugin';
            $registry = new ComponentRegistry($this->controller);
            $this->Log   = new LogComponent($registry, ['vars' => ['plugin' => $this->plugin]]);

            $this->assertTrue( $this->Log->write('info', 'scope', 'Hey man', ['plugin' => 'Yo']) );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('Yo', $result->context['plugin']);
        }

    /**
        }====> emergency() <====={
     */
        public function testEmergency() {
            $this->assertTrue( $this->Log->emergency('scope', 'Emergency message') );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('emergency', $result->level);
            $this->assertEquals('Emergency message', $result->message);
        }

    /**
        }====> alert() <====={
     */
        public function testAlert() {
            $this->assertTrue( $this->Log->alert('scope', 'alert message') );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('alert', $result->level);
            $this->assertEquals('alert message', $result->message);
        }

    /**
        }====> critical() <====={
     */
        public function testCritical() {
            $this->assertTrue( $this->Log->critical('scope', 'critical message') );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('critical', $result->level);
            $this->assertEquals('critical message', $result->message);
        }

    /**
        }====> error() <====={
     */
        public function testError() {
            $this->assertTrue( $this->Log->error('scope', 'error message') );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('error', $result->level);
            $this->assertEquals('error message', $result->message);
        }

    /**
        }====> warning() <====={
     */
        public function testWarning() {
            $this->assertTrue( $this->Log->warning('scope', 'warning message') );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('warning', $result->level);
            $this->assertEquals('warning message', $result->message);
        }

    /**
        }====> notice() <====={
     */
        public function testNotice() {
            $this->assertTrue( $this->Log->notice('scope', 'notice message') );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('notice', $result->level);
            $this->assertEquals('notice message', $result->message);
        }

    /**
        }====> debug() <====={
     */
        public function testDebug() {
            $this->assertTrue( $this->Log->debug('scope', 'debug message') );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('debug', $result->level);
            $this->assertEquals('debug message', $result->message);
        }

    /**
        }====> info() <====={
     */
        public function testInfo() {
            $this->assertTrue( $this->Log->info('scope', 'info message') );

            $Table = TableRegistry::get('Logging.Logs');
            $result = $Table->find('all')->last();

            $this->assertEquals('info', $result->level);
            $this->assertEquals('info message', $result->message);
        }
}
