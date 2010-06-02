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
 * @abstract 
 */

abstract class WpOption
{
	/**
	 * Post
	 * @var stdClass $post
	 * @access protected
	 */
	protected $post = null;
	
	/**
	 * De que lugar deber� extraer la informaci�n el campo
	 * @access protected
	 * @var int 
	 */
	protected $dbSource = 0;
	
	/**
	 * @var boolean
	 * @access protected
	 */
	protected $isMultiple = false;
	
	/**
	 * Nombre de la variable dentro del formulario
	 * @var string|int|boolean
	 * @access protected
	 */
	protected $name;
	
	/**
	 * @var string
	 * @access public
	 */
	protected $parent = '__root__';
	
	/**
	 * El campo que se requiere tenga un valor verdadero, para que la opcion pueda ser mostrada
	 * en el metabox
	 * @var string
	 */	
	protected $require = null;
	
	/**
	 * Valor por default de la opci�n
	 * @var string|int|boolean
	 * @access protected
	 */
	protected $defaultValue = null;
	
	/**
	 * Valor previamente guardado en la base de datos
	 *
	 * @var string|int|boolean
	 * @access protected
	 */
	protected $savedValue;
	
	/**
	 * Titulo de la opci�n
	 * @var string
	 * @access protected
	 */
	protected $title;
	
	/**
	 * Descripci�n de la opci�n almacenada
	 *
	 * @var string
	 * @access protected
	 */
	protected $description;
	
	/**
	 * Nombre del arreglo maestro que guarda los valores del formulario
	 * @var string
	 * @access protected
	 */
	protected $inputName;
	
	/**
	 * Dise�o con el cual se generar� la vista
	 * @var string
	 * @access protected
	 */
	protected $template;
	
	/**
	 * Determina si la opci�n aparecer� en el metabox
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $metabox = false;
	
	/**
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $hideInOptions = false;
	
	/**
	 * Las diferentes opciones que puede contener una opcion
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $options = array();
	
	/**
	 * Valor de la opcion 
	 * @var mixed|string|int|boolean
	 * @access protected
	 */
	protected $value = null;
	
	/**
	 * Option Childs
	 * @var mixed
	 * @access protected
	 */
	protected $childs = array();
	
	/**
	 * @var boolean
	 * @access protected
	 */
	protected $visible = true;
	
	/**
	 * Constructor de la clase
	 *
	 * @param string $name
	 * @param string $defaultValue
	 * @access public
	 */
	public function __construct($name, $defaultValue)
	{
		$this->name = $name;
		$this->defaultValue = $defaultValue;
	}
	
	/**
	 * Regresa la opci�n para ser impresa en el formulario
	 * @return  string $template
	 * @access public
	 */
	public function __toString()
	{
		$this->title = ($this->title) ? $this->title : $this->name;
		$this->template = str_replace('%title%', $this->title, $this->template);
		$this->template = str_replace('%input%', $this->___toString(), $this->template);
		$this->template = str_replace('%description%', $this->description, $this->template);
		$this->template = str_replace('%visible%', (! $this->visible ? ' style="display:none;"' : ''), $this->template);
		$this->template = str_replace('%class%', ($this->parent != '__root__' ? 'child_' . $this->parent : ''), $this->template);
		return $this->template;
	}
	
	/**
	 * Obtiene el valor almacenado en la base de datos
	 * @return string|mixed|int
	 * @access protected
	 */
	public function getStoredValue()
	{
		if($this->dbSource == self::$Sources['OPTION'])
			return get_option($this->inputName . '_' . $this->name);
		else if($this->dbSource == self::$Sources['POST_META'])
			return get_post_meta($this->post->ID, $this->name . '_value', true);
		else
			return '';
	}
	
	/**
	 * Obtiene el nombre que utilizar� en el formulario
	 * @return sttring
	 * @access public
	 */
	public function getFormName()
	{
		if($this->dbSource == self::$Sources['OPTION'])
			return $this->inputName.'['.$this->name.']';
		else if($this->dbSource == self::$Sources['POST_META'])
			return $this->name.'_value';
		else
			return '';
	}
	
	/**
	 * Metodo abstracto que regresa el input
	 * @access public
	 */
	public abstract function ___toString();
	
	/**
	 * M�todo que regresa el valor que se almacenar� en la base de datos, 
	 * dependiendo de la forma en que necesita ser guardado
	 * @param int|string|mixed $value
	 * @return int|string|mixed
	 * @access public
	 */
	public function set($value)
	{
		return $value;
	}
	
	/**
	 * Regresa el valor guardado o el default si no existe
	 * @access public 
	 */
	public function getValue()
	{
		if($this->value == null)
		{
			$this->savedValue = get_option($this->inputName . '_' . $this->name);
			$this->value = ($this->savedValue !== false) ? $this->savedValue : (($this->defaultValue !== false) ? $this->defaultValue : '');
		}
		return $this->value;
	}
	
	/**
	 * Guarda el nombre de la variable que almacena el formulario completo
	 * @param string $inputName
	 * @access public
	 */
	public function setInputName($inputName)
	{
		$this->inputName = $inputName;
	}
	
	/**
	 * @param string $description
	 * @access public
	 */
	public function setDescription($description)
	{
		$this->description = $description ? '<p>' . _($description) . '</p>' : '';
	}
	
	/**
	 * @param string $template
	 * @access public
	 */
	public function setTemplate($template)
	{
		$this->template = $template;
	}
	
	/**
	 * @param string $title
	 * @access public
	 */
	public function setTitle($title)
	{
		$this->title = _($title);
	}
	
	/**
	 * @param mixed $options
	 * @access public
	 */
	public function setOptions($options)
	{
		$this->options = $options;
	}
	
	/**
	 * Guarda la opcion metabox para que el elemento se despliege como un metabox
	 * @access public
	 */
	public function addMetabox()
	{
		$this->metabox = true;
	}
	
	/**
	 * @return boolean
	 */
	public function isMetaBox()
	{
		return $this->metabox;
	}
	
	/**
	 * @param boolean $isMultiple
	 * @access public
	 */
	public function setIsMultiple($isMultiple)
	{
		$this->isMultiple = $isMultiple;
	}
	
	/**
	 * @return boolean
	 * @access public
	 */
	public function getHideInOptions()
	{
		return $this->hideInOptions;
	}
	
	/**
	 * @param boolean $hideInOptions
	 * @access public
	 */
	public function setHideInOptions($hideInOptions)
	{
		$this->hideInOptions = $hideInOptions;
	}
	
	/**
	 * Agrega una opcion dentro del arbol jer�rquico
	 *
	 * @param WpOption $child
	 * @access public
	 */
	public function addChild($child)
	{
		$this->childs[] = $child;
	}
	
	/**
	 * @return mixed
	 * @access public
	 */
	public function getChilds()
	{
		return $this->childs;
	}
	
	/**
	 * @return boolean
	 * @access public
	 */
	public function hasChilds()
	{
		return (count($this->childs) > 0) ? true : false;
	}
	
	/**
	 * @return string
	 * @access public
	 */
	public function getParent()
	{
		return $this->parent;
	}
	
	/**
	 * @param string $parent
	 * @access public
	 */
	public function setParent($parent)
	{
		$this->parent = $parent;
	}
	
	/**
	 * @return string|int|boolean
	 * @access public
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @return boolean
	 * @access public
	 */
	public function isVisible()
	{
		return $this->visible;
	}
	
	/**
	 * @param boolean $visible
	 * @access public
	 */
	public function setVisible($visible)
	{
		$this->visible = $visible;
	}
	
	/**
	 * @return string|int|boolean
	 */
	public function getDefaultValue()
	{
		return $this->defaultValue;
	}
	
	/**
	 * @return string|int|boolean
	 */
	public function getSavedValue()
	{
		return $this->savedValue;
	}
	
	/**
	 * @param string|int|boolean $defaultValue
	 */
	public function setDefaultValue($defaultValue)
	{
		$this->defaultValue = $defaultValue;
	}
	
	/**
	 * @param string|int|boolean $savedValue
	 */
	public function setSavedValue($savedValue)
	{
		$this->savedValue = $savedValue;
	}
	
	/**
	 * @param mixed|string|int|boolean $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	/**
	 * @param int $dbSource
	 */
	public function setDbSource($dbSource)
	{
		$this->dbSource = $dbSource;
	}
	
	/**
	 * @var mixed
	 */
	public static $Sources = array(
		'OPTION' => 1, 
		'POST_META' => 2);
	
	/**
	 * @param stdClass $post
	 */
	public function setPost($post)
	{
		$this->post = $post;
	}
	
	/**
	 * @return string
	 */
	public function getRequire()
	{
		return $this->require;
	}
	
	/**
	 * @param string $require
	 */
	public function setRequire($require)
	{
		$this->require = $require;
	}


  const VERSION = "1.0";
	
}