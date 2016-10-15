<?php
/**
 * 客户端仓库类
 *
 * @author: memory<service@im050.com>
 */

namespace Storages;


class ClientStorage extends Storage
{

    protected $_storage;

    public function __construct()
    {
        if ($this->_storage == NULL) {
            $this->_storage = Storage::getInstance('ClientStorage');
        }
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->_storage, $name)) {
            return call_user_func_array(array($this->_storage, $name), $arguments);
        } else {
            return false;
        }
    }

}