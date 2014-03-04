<?php

/**
 * Adminhtml grid field example
 *
 * @category   Webinse
 * @package    Webinse_ConfigGrid
 * @author     Webinse Team <info@webinse.com>
 */
class Webinse_ConfigGrid_Block_Adminhtml_Form_Field_GridItem extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    /**
     * @var Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected $_groupRenderer;

    /**
     * Retrieve currency column renderer
     *
     * @return Webinse_ConfigGrid_Block_Adminhtml_Form_Field_Currency
     */
    protected function _getCurrencyRenderer()
    {
        if (!$this->_currencyRenderer) {
            $this->_currencyRenderer = $this->getLayout()->createBlock(
                    'wb_configgrid/adminhtml_form_field_currency', '', array('is_render_to_js_template' => true)
            );
            $this->_currencyRenderer->setClass('select_example');
            $this->_currencyRenderer->setExtraParams('style="width:100px"');
        }
        return $this->_currencyRenderer;
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('select_example', array(
            'label'    => $this->__('Select (Currency)'),
            'renderer' => $this->_getCurrencyRenderer(),
        ));
        $this->addColumn('input_example', array(
            'label' => Mage::helper('cataloginventory')->__('Input'),
            'style' => 'width:100px',
        ));
        $this->addColumn('password_example', array(
            'label' => $this->__('Password'),
            'style' => 'width:100px',
        ));
        $this->_addAfter       = false;
        $this->_addButtonLabel = Mage::helper('cataloginventory')->__('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
                'option_extra_attr_' . $this->_getCurrencyRenderer()->calcOptionHash($row->getData('select_example')), 'selected="selected"'
        );
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName]))
            throw new Exception('Wrong column name specified.');

        $column    = $this->_columns[$columnName];
        $inputName = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if ($column['renderer'])
            return $column['renderer']->setInputName($inputName)->setColumnName($columnName)->setColumn($column)->toHtml();

        if ($columnName === 'password_example') {
            return '<input type="password" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
                    ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
                    (isset($column['class']) ? $column['class'] : 'input-text') . '"' .
                    (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '/>';
        }
        else {
            return parent::_renderCellTemplate($columnName);
        }
    }

}
