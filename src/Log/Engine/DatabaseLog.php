<?php
namespace Logging\Log\Engine;
use Cake\Log\Engine\BaseLog;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class DatabaseLog extends BaseLog {

    /**
     * Default config for this class
     *
     * @var array
     */
    protected $_defaultConfig = [
        'levels'        => [],
        'scopes'        => [],
        'requiredScope' => false,
        'model'         => 'Logging.Logs',
        'table'         => 'logs',
        'userId'        => 'Auth.User.id',
    ];

    protected $_context = [];

    public function __construct($config = []) {
        parent::__construct($config);
    }

    /**
     * Implements writing to log files.
     *
     * @param string $level The severity level of the message being written.
     *    See Cake\Log\Log::$_levels for list of possible levels.
     * @param string $message The message you want to log.
     * @param array $context Additional information about the logged message
     * @return bool success of write.
     */
    public function log($level, $message, array $context = []) {

        if ( $this->config('requiredScope') && ( empty($context['scope']) ) )
            return false;

        $scopes = ( empty($context['scope']) ) ? [null] : $context['scope'];
        unset($context['scope']);
        $this->_context = $context;

        $Table = TableRegistry::get($this->config('model'), ['table' => $this->config('table')]);

        foreach ( $scopes as $scope ) {
            $entity = $Table->newEntity();

            $data = [
                'level'   => $level,
                'user_id' => $this->_userId(),
                'scope'   => $scope,
                'message' => $message,
                'context' => $this->_context,
            ];

            $entity = $Table->patchEntity($entity, $data);

            $Table->save($entity);
        }

        return true;
    }

    /**
     * Get user_id
     * @return int
     */
    protected function _userId() {
        if ( array_key_exists('userId', $this->_context) ) {
            $userId = ($this->_context['userId']) ? (int) $this->_context['userId'] : null;
            unset($this->_context['userId']);
            return $userId;
        }

        if ( isset($_SESSION) && is_array($_SESSION) )
            return Hash::get($_SESSION, $this->config('userId'));

        return null;
    }
}