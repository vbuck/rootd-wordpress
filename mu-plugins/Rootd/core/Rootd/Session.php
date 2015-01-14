<?php

/**
 * Generic session model.
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Session extends Rootd_Object
{

    const MESSAGE_TYPE_DEFAULT      = 'updated';//'info';
    const MESSAGE_TYPE_WARNING      = 'update-nag';//'warning';
    const MESSAGE_TYPE_ERROR        = 'error';//'danger';

    protected $_dataKey             = '_ROOTD_SESSION_';
    protected $_messagesDataKey     = '_ROOTD_MESSAGES_';

    public function _construct()
    {
        if (!isset($_SESSION[$this->_dataKey])) {
            $_SESSION[$this->_dataKey] = array();
        }

        if (!isset($_SESSION[$this->_messagesDataKey])) {
            $_SESSION[$this->_messagesDataKey] = array();
        }
    }

    public function addError($message)
    {
        return $this->addMessage($message, self::MESSAGE_TYPE_ERROR);
    }

    public function addMessage($message, $type = self::MESSAGE_TYPE_DEFAULT)
    {
        $id             = md5($message);
        $allMessages    = $this->getData($this->_messagesDataKey);

        if (!is_array($allMessages)) {
            $allMessages = array();
        } else if(!isset($allMessages[$type])) {
            $allMessages[$type] = array();
        }

        $allMessages[$type][$id] = $message;

        $this->setData($this->_messagesDataKey, $allMessages);

        return $this;
    }

    public function addWarning($message)
    {
        return $this->addMessage($message, self::MESSAGE_TYPE_WARNING);
    }

    public function clearMessages()
    {
        $this->getAllMessages();
        
        return $this;
    }

    public function getAllMessages()
    {
        $allMessages = array(
            self::MESSAGE_TYPE_DEFAULT  => $this->getMessages(self::MESSAGE_TYPE_DEFAULT, true),
            self::MESSAGE_TYPE_WARNING  => $this->getMessages(self::MESSAGE_TYPE_WARNING, true),
            self::MESSAGE_TYPE_ERROR    => $this->getMessages(self::MESSAGE_TYPE_ERROR, true),
        );

        return $allMessages;
    }

    public function getData($key = null) 
    {
        if (is_null($key)) {
            return $_SESSION[$this->_dataKey];
        }
        
        if (isset($_SESSION[$this->_dataKey][$key])) {
            return $_SESSION[$this->_dataKey][$key];
        }
        
        return null;
    }

    public function getMessages($type = self::MESSAGE_TYPE_DEFAULT, $clear = true)
    {
        $allMessages = $this->getData($this->_messagesDataKey);

        if ($clear) {
            $clone = $allMessages;
            $clone[$type] = array();

            $this->setData($this->_messagesDataKey, $clone);

            unset($clone);
        }

        if (!is_array($allMessages)) {
            return array();
        } else if (!isset($allMessages[$type])) {
            return array();
        }

        $messages = $allMessages[$type];

        unset($allMessages);

        return $messages;
    }

    public function setData($key, $value = null)
    {
        if (is_array($key) && is_null($value)) {
            foreach ($key as $_key => $_value) {
                $this->setData($_key, $_value);
            }

            return $this;
        }

        $_SESSION[$this->_dataKey][$key] = $value;

        return $this;
    }

    /**
     * Unset data from the session.
     *
     * @param string $key The data key.
     * 
     * @return Rootd_Object
     */
    public function unsetData($key = null)
    {
        if (is_null($key)) {
            $_SESSION[$this->_dataKey] = array();
        } else {
            unset($_SESSION[$this->_dataKey][$key]);
        }
        
        return $this;
    }

}