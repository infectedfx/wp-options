<?php
/**
 * Spiga
 *
 * wp-options
 *
 * @category   Wordpress
 * @package    Storelicious_Themes
 * @copyright  Copyright (c) 2008-2010 Spiga (http://www.spiga.mx)
 * @author     zetta (http://www.ctrl-zetta.com)
 * @version    1.1
 */

class WpColorPickerOption extends WpOption
{
    protected $emptyValue = '000000';
    
    /**
     * Constructor de la clase
     *
     * @param string $name
     * @param string $defaultValue
     * @return WpColorPickerOption
     */
    public function WpColorPickerOption($name, $defaultValue)
    {
        parent::__construct($name, $defaultValue);
    }
    
    /**
     * Genera el html de la opción
     * @return string
     * @access public
     */
    public function ___toString()
    {
        $value = $this->getValue();
        return "<input  id='{$this->getFormId()}' class='wpColorPickerOption' type='text' maxlength='6' size='6' name='{$this->getFormName()}' value='{$value}' />";
    }
}
