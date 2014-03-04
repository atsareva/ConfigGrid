<?php

/**
 * HTML select element block with currencies
 *
 * @category   Webinse
 * @package    Webinse_ConfigGrid
 * @author     Webinse Team <info@webinse.com>
 */
class Webinse_ConfigGrid_Block_Adminhtml_Form_Field_Currency extends Mage_Core_Block_Html_Select
{

    /**
     * Currency cache
     *
     * @var array
     */
    private $_currencies;

    /**
     * Retrieve available currency
     *
     * @return array
     */
    protected function _getAvailableCurrency()
    {
        if (is_null($this->_currencies)) {
            $this->_currencies = array();
            $collection        = Mage::app()->getStore()->getAvailableCurrencyCodes(true);
            foreach ($collection as $item) {
                /* @var $item Mage_Customer_Model_Group */
                $this->_currencies[$item] = $item;
            }
        }
        return $this->_currencies;
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->addOption(0, $this->__('--Select--'));

            foreach ($this->_getAvailableCurrency() as $currencyCode)
                $this->addOption($currencyCode, addslashes($currencyCode));
        }
        return parent::_toHtml();
    }

}
