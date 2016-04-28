<?php
namespace Logging\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Log\Log;

/**
 * Log component
 */
class LogComponent extends Component {

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'request' => false,
        'session' => false,
    ];

    /**
     * Writes the given message and type to all of the configured log adapters.
     * Configured adapters are passed both the $level and $message variables. $level
     * is one of the following strings/values.
     *
     * ### Levels:
     *
     * - `LOG_EMERG` => 'emergency',
     * - `LOG_ALERT` => 'alert',
     * - `LOG_CRIT` => 'critical',
     * - `LOG_ERR` => 'error',
     * - `LOG_WARNING` => 'warning',
     * - `LOG_NOTICE` => 'notice',
     * - `LOG_INFO` => 'info',
     * - `LOG_DEBUG` => 'debug',
     *
     * ### Basic usage
     *
     * Write a 'warning' message to the logs:
     *
     * ```
     * Log::write('warning', 'Stuff is broken here');
     * ```
     *
     * ### Using scopes
     *
     * When writing a log message you can define one or many scopes for the message.
     * This allows you to handle messages differently based on application section/feature.
     *
     * ```
     * Log::write('warning', 'Payment failed', ['scope' => 'payment']);
     * ```
     *
     * When configuring loggers you can configure the scopes a particular logger will handle.
     * When using scopes, you must ensure that the level of the message, and the scope of the message
     * intersect with the defined levels & scopes for a logger.
     *
     * ### Unhandled log messages
     *
     * If no configured logger can handle a log message (because of level or scope restrictions)
     * then the logged message will be ignored and silently dropped. You can check if this has happened
     * by inspecting the return of write(). If false the message was not handled.
     *
     * @param int|string $level The severity level of the message being written.
     *    The value must be an integer or string matching a known level.
     * @param mixed $message Message content to log
     * @param string|array $scope key can be passed to be used for further filtering
     * @param array $context Additional data to be used for logging the message.
     *  See Cake\Log\Log::config() for more information on logging scopes.
     * @param  mixed $config : change base config (ex request, session...)
     * @return bool Success
     * @throws \InvalidArgumentException If invalid level is passed.
     */
    public function write($level, $scope, $message, $context = [], $config = []) {

        $config['request'] = ( isset($config['request']) ) ? $config['request'] : $this->config('request');
        $config['session'] = ( isset($config['session']) ) ? $config['session'] : $this->config('session');

        if ( $config['request'] )
            $context['request'] = $this->request;

        if ( $config['session'] )
            $context['session'] = $this->request->session()->read();

        $context['scope'] = (array) $scope;

        return Log::write($level, $message, $context);
    }

    /**
     * Convenience method to log emergency messages
     *
     * @param string|array $scope key can be passed to be used for further filtering
     * @param string $message log message
     * @param array $context Additional data to be used for logging the message.
     *  See Cake\Log\Log::config() for more information on logging scopes.
     * @param  mixed $saveRequest : (true) add request in context /// (null) add request if config('request') = true
     * @param  mixed $saveSession : (true) add session in context /// (null) add session if config('saveSession') = true
     * @return bool Success
     */
    public function emergency($scope, $message, $context = [], $saveRequest = null, $saveSession = null) {
        return $this->write('emergency', $scope, $message, $context, $saveRequest, $saveSession);
    }

    /**
     * Convenience method to log alert messages
     *
     * @param string|array $scope key can be passed to be used for further filtering
     * @param string $message log message
     * @param array $context Additional data to be used for logging the message.
     *  See Cake\Log\Log::config() for more information on logging scopes.
     * @param  mixed $saveRequest : (true) add request in context /// (null) add request if config('request') = true
     * @param  mixed $saveSession : (true) add session in context /// (null) add session if config('saveSession') = true
     * @return bool Success
     */
    public function alert($scope, $message, $context = [], $saveRequest = null, $saveSession = null) {
        return $this->write('alert', $scope, $message, $context, $saveRequest, $saveSession);
    }

    /**
     * Convenience method to log critical messages
     *
     * @param string|array $scope key can be passed to be used for further filtering
     * @param string $message log message
     * @param array $context Additional data to be used for logging the message.
     *  See Cake\Log\Log::config() for more information on logging scopes.
     * @param  mixed $saveRequest : (true) add request in context /// (null) add request if config('request') = true
     * @param  mixed $saveSession : (true) add session in context /// (null) add session if config('saveSession') = true
     * @return bool Success
     */
    public function critical($scope, $message, $context = [], $saveRequest = null, $saveSession = null) {
        return $this->write('critical', $scope, $message, $context, $saveRequest, $saveSession);
    }

    /**
     * Convenience method to log error messages
     *
     * @param string|array $scope key can be passed to be used for further filtering
     * @param string $message log message
     * @param array $context Additional data to be used for logging the message.
     *  See Cake\Log\Log::config() for more information on logging scopes.
     * @param  mixed $saveRequest : (true) add request in context /// (null) add request if config('request') = true
     * @param  mixed $saveSession : (true) add session in context /// (null) add session if config('saveSession') = true
     * @return bool Success
     */
    public function error($scope, $message, $context = [], $saveRequest = null, $saveSession = null) {
        return $this->write('error', $scope, $message, $context, $saveRequest, $saveSession);
    }

    /**
     * Convenience method to log warning messages
     *
     * @param string|array $scope key can be passed to be used for further filtering
     * @param string $message log message
     * @param array $context Additional data to be used for logging the message.
     *  See Cake\Log\Log::config() for more information on logging scopes.
     * @param  mixed $saveRequest : (true) add request in context /// (null) add request if config('request') = true
     * @param  mixed $saveSession : (true) add session in context /// (null) add session if config('saveSession') = true
     * @return bool Success
     */
    public function warning($scope, $message, $context = [], $saveRequest = null, $saveSession = null) {
        return $this->write('warning', $scope, $message, $context, $saveRequest, $saveSession);
    }

    /**
     * Convenience method to log notice messages
     *
     * @param string|array $scope key can be passed to be used for further filtering
     * @param string $message log message
     * @param array $context Additional data to be used for logging the message.
     *  See Cake\Log\Log::config() for more information on logging scopes.
     * @param  mixed $saveRequest : (true) add request in context /// (null) add request if config('request') = true
     * @param  mixed $saveSession : (true) add session in context /// (null) add session if config('saveSession') = true
     * @return bool Success
     */
    public function notice($scope, $message, $context = [], $saveRequest = null, $saveSession = null) {
        return $this->write('notice', $scope, $message, $context, $saveRequest, $saveSession);
    }

    /**
     * Convenience method to log debug messages
     *
     * @param string|array $scope key can be passed to be used for further filtering
     * @param string $message log message
     * @param array $context Additional data to be used for logging the message.
     *  See Cake\Log\Log::config() for more information on logging scopes.
     * @param  mixed $saveRequest : (true) add request in context /// (null) add request if config('request') = true
     * @param  mixed $saveSession : (true) add session in context /// (null) add session if config('saveSession') = true
     * @return bool Success
     */
    public function debug($scope, $message, $context = [], $saveRequest = null, $saveSession = null) {
        return $this->write('debug', $scope, $message, $context, $saveRequest, $saveSession);
    }

    /**
     * Convenience method to log info messages
     *
     * @param string|array $scope key can be passed to be used for further filtering
     * @param string $message log message
     * @param array $context Additional data to be used for logging the message.
     *  See Cake\Log\Log::config() for more information on logging scopes.
     * @param  mixed $saveRequest : (true) add request in context /// (null) add request if config('request') = true
     * @param  mixed $saveSession : (true) add session in context /// (null) add session if config('saveSession') = true
     * @return bool Success
     */
    public function info($scope, $message, $context = [], $saveRequest = null, $saveSession = null) {
        return $this->write('info', $scope, $message, $context, $saveRequest, $saveSession);
    }
}
