<?php
/**
 * Spiga
 *
 * SpigaTheme
 *
 * @category   Wordpress
 * @package    WordPress_Themes
 * @copyright  Copyright (c) 2008-2009 Spiga (http://www.spiga.com.mx)
 * @author     zetta (http://www.ctrl-zetta.com)
 * @version    1.0
 */

class WpSelectCategoriesOption extends WpOption
{
	/**
	 * Constructor de la clase
	 *
	 * @param string $name
	 * @param int|mixed $defaultValue
	 * @return WpSelectCategoriesOption
	 */
	function WpSelectCategoriesOption($name, $defaultValue)
	{
		parent::__construct($name, $defaultValue);
	}
	
	/**
	 * Genera el html de la opci�n
	 * @return string
	 * @access public
	 */
	function ___toString()
	{
		$this->options = get_categories(array(
			'hide_empty' => false));
		$this->savedValue = $this->getStoredValue();
		$value = ($this->savedValue !== false) ? $this->savedValue : (($this->defaultValue !== null) ? $this->defaultValue : '');
		$formName = $this->getFormName();
		if($this->isMultiple)
		{
			$input = "<select name=\"{$formName}[]\" multiple=\"multiple\" size=\"5\" value=\"{$value}\" />";
			$value = ($value) ? $value : array();
		} else
			$input = "<select name=\"{$formName}\" value=\"{$value}\" />";
		
		if(! $this->isMultiple)
		{
			$input .= "<option value=\"0\">" . _('Select one category') . "</option>";
			foreach($this->options as $category)
				$input .= "\n<option value=\"{$category->cat_ID}\" " . ($category->cat_ID == $value ? 'selected="selected"' : '') . " >" . _($category->name) . '</option>';
		} else
			foreach($this->options as $category)
				$input .= "\n<option value=\"{$category->cat_ID}\" " . (in_array($category->cat_ID, $value) ? 'selected="selected"' : '') . " >" . _($category->name) . '</option>';
		
		$input .= "</select>";
		return $input;
	}

}

