<?php

/**
 * Backend for serialized array data
 *
 * @category   Webinse
 * @package    Webinse_ConfigGrid
 * @author     Webinse Team <info@webinse.com>
 */
class Webinse_ConfigGrid_Model_System_Config_Backend_GridItem extends Mage_Core_Model_Config_Data
{

    /**
     * Process data after load
     */
    protected function _afterLoad()
    {
        $value = Mage::helper('wb_configgrid/gridItems')->makeArrayFieldValue($this->getValue());
        $this->setValue($value);
    }

    /**
     * Prepare data before save
     */
    protected function _beforeSave()
    {
        $value = Mage::helper('wb_configgrid/gridItems')->makeStorableArrayFieldValue($this->getValue());
        $this->setValue($value);
    }

}
