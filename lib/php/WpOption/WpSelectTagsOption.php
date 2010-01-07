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

class WpSelectTagsOption extends WpOption
{
	/**
	 * Constructor de la clase
	 *
	 * @param string $name
	 * @param int|mixed $defaultValue
	 * @return WpSelectTagsOption
	 */
	public function WpSelectTagsOption($name, $defaultValue)
	{
		parent::__construct($name, $defaultValue);
	}
	
	/**
	 * Genera el html de la opci�n
	 * @return string
	 * @access public
	 */
	public function ___toString()
	{
		$this->options = get_tags(array(
			'hide_empty' => false));
		$this->savedValue = $this->getStoredValue();
		$value = ($this->savedValue !== false) ? $this->savedValue : (($this->defaultValue !== null) ? $this->defaultValue : '');
		
		if($this->isMultiple)
		{
			$input = "<select name=\"{$this->getFormName()}[]\" multiple=\"multiple\" size=\"5\" value=\"{$value}\" />";
			$value = ($value) ? $value : array();
		} else
			$input = "<select name=\"{$this->getFormName()}\" value=\"{$value}\" />";
		
		if(! $this->isMultiple)
		{
			$input .= "<option value=\"0\">" . _('Select one page') . "</option>";
			foreach($this->options as $tag)
				$input .= "\n<option value=\"{$tag->name}\" " . ($tag->name == $value ? 'selected="selected"' : '') . " >" . _($tag->name) . '</option>';
		} else
			foreach($this->options as $tag)
				$input .= "\n<option value=\"{$tag->name}\" " . (in_array($tag->name, $value) ? 'selected="selected"' : '') . " >" . _($tag->name) . '</option>';
		
		$input .= "</select>";
		return $input;
	}
}

