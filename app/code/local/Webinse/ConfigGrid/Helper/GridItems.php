<?php

/**
 * GridItems value manipulation helper
 *
 * @category   Webinse
 * @package    Webinse_ConfigGrid
 * @author     Webinse Team <info@webinse.com>
 */
class Webinse_ConfigGrid_Helper_GridItems
{

    /**
     * @var string
     */
    protected $_obscuredValue = '******';

    /**
     * Generate a storable representation of a value
     *
     * @param mixed $value
     * @return string
     */
    protected function _serializeValue($value)
    {
        if (is_array($value)) {
            $data = array();
            foreach ($value as $selectCode => $keys) {
                if (!array_key_exists($selectCode, $data)) {
                    $data[$selectCode] = array(
                        'input_example'    => $keys['input_example'],
                        'password_example' => $keys['password_example'],
                    );
                }
            }
            if (count($data) == 1 && array_key_exists(0, $data)) {
                return (string) $data[0];
            }

            return serialize($data);
        }
        else
            return '';
    }

    /**
     * Create a value from a storable representation
     *
     * @param mixed $value
     * @return array
     */
    protected function _unserializeValue($value)
    {
        if (is_string($value) && !empty($value)) {
            return unserialize($value);
        }
        else {
            return array();
        }
    }

    /**
     * Check whether value is in form retrieved by _encodeArrayFieldValue()
     *
     * @param mixed
     * @return bool
     */
    protected function _isEncodedArrayFieldValue($value)
    {
        if (!is_array($value)) {
            return false;
        }

        unset($value['__empty']);
        foreach ($value as $_id => $row) {
            if (!is_array($row) || !array_key_exists('select_example', $row) || !array_key_exists('input_example', $row) || !array_key_exists('password_example', $row)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Encode value to be used in Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
     *
     * @param array
     * @return array
     */
    protected function _encodeArrayFieldValue(array $value)
    {
        $result = array();
        foreach ($value as $selectCode => $keys) {
            $_id          = Mage::helper('core')->uniqHash('_');
            $result[$_id] = array(
                'select_example'   => $selectCode,
                'input_example'    => $keys['input_example'],
                'password_example' => $this->_obscuredValue,
            );
        }
        return $result;
    }

    /**
     * Decode value from used in Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
     *
     * @param array
     * @return array
     */
    protected function _decodeArrayFieldValue(array $value)
    {
        $result = array();
        unset($value['__empty']);

        $storeId = Mage::app()->getStore()->getStoreId();

        foreach ($value as $_id => $row) {
            if (!is_array($row) || !array_key_exists('select_example', $row) || !array_key_exists('input_example', $row) || !array_key_exists('password_example', $row)) {
                continue;
            }

            $result[$row['select_example']] = array(
                'input_example'    => $row['input_example'],
                'password_example' => $this->exampleData($row['password_example'], 'password_example', $row['select_example'], $storeId),
            );
        }
        return $result;
    }

    /**
     * Retrieve data by select code
     *
     * @param string $value
     * @param string $key
     * @param string $selectCode
     * @param mixed $store
     * @return string
     */
    public function exampleData($value, $key, $selectCode, $store = null)
    {
        if ($value != $this->_obscuredValue && stripos($key, 'password') !== false) {
            return Mage::helper('core')->encrypt($value);
        }

        return $this->getConfigValue($key, $selectCode, $store);
    }

    /**
     * Retrieve exampe data from config by select code
     *
     * @param string $key
     * @param string $selectCode
     * @param mixed $store
     * @return string
     */
    public function getConfigValue($key, $selectCode, $store = null)
    {
        $configValue = $this->_unserializeValue(Mage::getStoreConfig('webinse_configgrid/grid/item', $store));

        if (array_key_exists($selectCode, $configValue) && array_key_exists($key, $configValue[$selectCode])) {
            return $configValue[$selectCode][$key];
        }

        return '';
    }

    /**
     * Make value readable by Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
     *
     * @param mixed $value
     * @return array
     */
    public function makeArrayFieldValue($value)
    {
        $value = $this->_unserializeValue($value);
        if (!$this->_isEncodedArrayFieldValue($value)) {
            $value = $this->_encodeArrayFieldValue($value);
        }

        return $value;
    }

    /**
     * Make value ready for store
     *
     * @param mixed $value
     * @return string
     */
    public function makeStorableArrayFieldValue($value)
    {
        if ($this->_isEncodedArrayFieldValue($value)) {
            $value = $this->_decodeArrayFieldValue($value);
        }

        $value = $this->_serializeValue($value);
        return $value;
    }

    /**
     * Retrieve config value (example data) by key
     *
     * @param string $key
     * @return string
     */
    public function getConfigValueByKey($key)
    {
        $selectCode  = Mage::app()->getStore()->getCurrentCurrencyCode();
        $storeId     = Mage::app()->getStore()->getStoreId();
        $configValue = $this->getConfigValue($key, $selectCode, $storeId);

        if ($configValue && stripos($key, 'password') !== false) {
            return Mage::helper('core')->decrypt($configValue);
        }

        return Mage::getStoreConfig('webinse_configgrid/grid/' . $key, $storeId);
    }

}
