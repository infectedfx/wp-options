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

/**
 * SpigaThemeOption
 */
require_once 'WpOption.php';

class WpOptions
{
    
    /**
     * Where the plugin are located
     * @var string $pluginLocation
     * @access private
     */
    private $themeLocation;
    
    /**
     * Determina si se han dado de alta campos para mostrarse
     * en el metabox
     *
     * @var boolean $hasMetaBoxData
     */
    private $hasMetaBoxData = false;
    
    /**
     * File Location
     *
     * @var string
     * @access private
     */
    private $file;
    
    /**
     * Theme Name
     * @var string $themeName
     * @access private
     */
    private $themeName;
    
    /**
     * BaseThemeName
     * @var string
     * @access private
     */
    private $baseThemeName;
    
    /**
     * Options Container
     * @var mixed $options
     * @access private
     */
    private $options = array();
    
    /**
     * MetabOx Options Container
     * @var mixed $options
     * @access private
     */
    private $optionsInMetaBox = array();
    /**
     * Contenido 
     * @var string $content
     * @access private
     */
    private $content = "\n\n<!-- SpigaThemeOptions Generator v1 -->\n\n\t\t";
    
    /**
     * Hojas de estilo utilizadas
     * @var mixed $css
     * @access private
     */
    private $css = array();
    
    /**
     * Version utilizada de Wordpress
     *
     * @var float
     * @access private
     */
    private $wpVersion = 0;
    
    /**
     * @var wpdb
     * @access private
     */
    private $wpdb;
    
    /**
     * Indica si las opciones del tema han sido actualizadas
     * @var boolean
     * @access priate
     */
    private $updated = false;
    
    /**
     * @var string $forumUrl
     * @access private
     */
    private $forumUrl = '';
    
    /**
     * @var string $manualUrl
     * @access private
     */
    private $manualUrl = '';
    
    /**
     * Instanc�a el objeto SpigaThemeOptions
     *
     * @param float $wpVersion
     * @param wpdb $wpdb
     * @return WpOptions WpOptions
     * @access public
     */
    public function WpOptions($wpVersion, $wpdb)
    {
        $this->wpVersion = $wpVersion;
        $this->wpdb = $wpdb;
        $this->file = __FILE__;
    }
    
    /**
     * Determina si se han dado de alta campos para mostrarse
     * en el metabox
     *
     * @return Boolean
     */
    public function hasMetaBox()
    {
        return $this->hasMetaBoxData;
    }
    
    /**
     * Agrega la p�gina de opciones en el administrador y la funcion del metabox si es necesaria
     * @access public
     */
    public function addOptionsPage()
    {
        add_menu_page(_('Configure ') . $this->themeName, $this->themeName, 'edit_themes', basename(__FILE__), $this->getFunctionScope('render'));
        if ($this->hasMetaBox())
        {
            add_meta_box('new-meta-boxes', $this->themeName . ' :: Post Settings', $this->getFunctionScope('renderMetaBox'), 'post', 'normal', 'high');
            add_action('save_post', $this->getFunctionScope('savePostData'));
        }
    }
    
    /**
     * Para aquello del callback, esta function no deberia existir, pero no me gusta como se formatea mi c�digo
     * con el ZendStuio cuando utilizo arrays tan peque�os... pero ya ni modo =P 
     *
     * @param string $funcName
     * @return mixed
     * @access private
     */
    private function getFunctionScope($funcName)
    {
        return array($this, $funcName);
    }
    
    /**
     * Agrega un titulo a las opciones
     *
     * @param string $title
     * @access public
     */
    public function addTitle($title)
    {
        require_once 'WpOption/WpOptionTitle.php';
        $title = new WpOptionTitle($title);
        $this->options[] = $title;
    }
    
    /**
     * Agrega un metabox en la pagina de post
     * @param string $metaBoxName Tiene que ser un nombre de opcion previamente creado
     * @param boolean $hideInOptionsPage Si es verdadero la opcion solo se mostrar� en el metabox y se ocultar� en la
     *        p�gina de opciones, en caso contrario se mostrar� en ambas
     * @throws Exception
     * @access public
     */
    public function addMetaBox($metaBoxName, $hideInOptionsPage = true)
    {
        if (! isset($this->options[$metaBoxName]))
            throw new Exception(_("Can't add new Metabox if the Option '{$metaBoxName}' doesn't exist"));
        
        $this->options[$metaBoxName]->addMetabox();
        $this->options[$metaBoxName]->setHideInOptions($hideInOptionsPage);
        $this->hasMetaBoxData = true;
        $this->optionsInMetaBox[] = $this->options[$metaBoxName];
    }
    
    /**
     * Agrega un metabox en la pagina de post
     * @param mixed $metaBoxName Arreglo con los nombres de las opciones previamente creadas
     * @param boolean $hideInOptionsPage Si es verdadero la opcion solo se mostrar� en el metabox y se ocultar� en la
     *        p�gina de opciones, en caso contrario se mostrar� en ambas
     * @throws Exception
     * @access public
     */
    public function addMetaBoxes($metaBoxNames, $hideInOptionsPage = true)
    {
        foreach ( $metaBoxNames as $metaBoxName )
        {
            
            if (! isset($this->options[$metaBoxName]))
                throw new Exception(_("Can't add new Metabox if the Option '{$metaBoxName}' doesn't exist"));
            
            $this->options[$metaBoxName]->addMetabox();
            $this->options[$metaBoxName]->setHideInOptions($hideInOptionsPage);
            $this->optionsInMetaBox[] = $this->options[$metaBoxName];
        }
        $this->hasMetaBoxData = true;
    }
    
    /**
     * Agrega un metabox en la pagina de post con un campo que condiciona si este se mostrar� o no
     *
     * @param string $metaBoxName El nombre de una opci�n previamente almacenada
     * @param string $condition El nombre de una opci�n previamente almacenada
     * @param boolean $hideInOptionsPage
     * @access public
     */
    public function addConditionalMetaBox($metaBoxName, $condition, $hideInOptionsPage = true)
    {
        if (! isset($this->options[$metaBoxName]))
            throw new Exception(_("Can't add new Metabox if the Option '{$metaBoxName}' doesn't exist"));
        
        if (! isset($this->options[$condition]))
            throw new Exception(_("Can't add new Metabox if the Option '{$condition}' doesn't exist"));
        
        if (get_class($this->options[$condition]) != 'WpCheckOption')
            throw new Exception(_("Can't add ConditionalMetaBoxes if the Option '{$condition}' doesn't a WpCheckOption Option"));
        
        $this->options[$metaBoxName]->addMetabox();
        $this->options[$metaBoxName]->setHideInOptions($hideInOptionsPage);
        $this->optionsInMetaBox[] = $this->options[$metaBoxName];
        $this->hasMetaBoxData = true;
        $this->options[$metaBoxName]->setRequire($condition);
    }
    
    /**
     * Agrega una condici�n a algunas opciones previamente almacenadas
     *
     * @param string $condition El nombre de una opci�n previamente almacenada
     * @param mixed $options
     * @throws Exception
     * @access public
     */
    public function setConditionalOptions($condition, $options)
    {
        if (! isset($this->options[$condition]))
            throw new Exception(_("Can't add ConditionalOptions if the Option '{$condition}' doesn't exist"));
        
        if (! isset($this->options[$condition]))
            throw new Exception(_("Can't add new Metabox if the Option '{$condition}' doesn't exist"));
        
        if (get_class($this->options[$condition]) != 'WpCheckOption')
            throw new Exception(_("Can't add ConditionalOptions if the Option '{$condition}' doesn't a WpCheckOption Option"));
        
        foreach ( $options as $option )
        {
            if (! isset($this->options[$option]))
                throw new Exception(_("Can't add ConditionalOption if the Option '{$option}' doesn't exist"));
            $this->options[$option]->setParent($condition);
            $this->options[$condition]->addChild($this->options[$option]);
        }
    }
    
    /**
     * Agrega una opci�n de tipo String (input)
     *
     * @param string $name
     * @param string $defaultValue
     * @param string [optional] $title
     * @param string [optional] $description
     * @access public
     */
    public function addStringOption($name, $defaultValue, $title = '', $description = '')
    {
        require_once 'WpOption/WpStringOption.php';
        $spigaOption = new WpStringOption($name, $defaultValue);
        $spigaOption->setTitle($title);
        $spigaOption->setDescription($description);
        $this->options[$name] = $spigaOption;
    }
    
    /**
     * Agrega una opci�n de tipo String (textarea)
     *
     * @param string $name
     * @param string $defaultValue
     * @param string [optional] $title
     * @param string [optional] $description
     * @access public
     */
    public function addTextOption($name, $defaultValue, $title = '', $description = '')
    {
        require_once 'WpOption/WpTextOption.php';
        $spigaOption = new WpTextOption($name, $defaultValue);
        $spigaOption->setTitle($title);
        $spigaOption->setDescription($description);
        $this->options[$name] = $spigaOption;
    }
    
    /**
     * Agrega una opci�n de tipo Boleano (input - radio [2 opciones])
     *
     * @param string $name
     * @param boolean $defaultValue
     * @param string [optional] $title
     * @param string [optional] $description
     * @access public
     */
    public function addBooleanOption($name, $defaultValue, $title = '', $description = '')
    {
        require_once 'WpOption/WpBooleanOption.php';
        $spigaOption = new WpBooleanOption($name, $defaultValue);
        $spigaOption->setTitle($title);
        $spigaOption->setDescription($description);
        $this->options[$name] = $spigaOption;
    }
    
    /**
     * Agrega una opci�n de tipo Entero (input)
     *
     * @param string $name
     * @param int $defaultValue
     * @param string [optional] $title
     * @param string [optional] $description
     * @access public
     */
    public function addNumberOption($name, $defaultValue, $title = '', $description = '')
    {
        require_once 'WpOption/WpNumberOption.php';
        $spigaOption = new WpNumberOption($name, $defaultValue);
        $spigaOption->setTitle($title);
        $spigaOption->setDescription($description);
        $this->options[$name] = $spigaOption;
    }
    
    /**
     * Agrega una opci�n multiple (input - radio [x opciones])
     *
     * @param string $name
     * @param mixed $options Array asociativo con los valores a mostrar
     * @param string|int|boolean $defaultValue
     * @param string [optional] $title
     * @param string [optional] $description
     * @param boolean [optional] $onePerLine (si deseamos que se muestre cada opcion en una linea diferente
     * @access public
     */
    public function addRadioOption($name, $options, $defaultValue, $title = '', $description = '', $onePerLine = true)
    {
        require_once 'WpOption/WpRadioOption.php';
        $spigaOption = new WpRadioOption($name, $defaultValue, $onePerLine);
        $spigaOption->setTitle($title);
        $spigaOption->setDescription($description);
        $spigaOption->setOptions($options);
        $this->options[$name] = $spigaOption;
    }
    
    /**
     * Agrega una opci�n multiple (input - checkbox [x opciones])
     *
     * @param string $name
     * @param mixed $selectedValues
     * @param mixed $options Array asociativo con los valores a mostrar
     * @param string [optional] $title
     * @param string [optional] $description
     * @param boolean [optional] $onePerLine (si deseamos que se muestre cada opcion en una linea diferente
     * @access public
     */
    public function addCheckBoxOption($name, $options, $selectedValues, $title = '', $description = '', $onePerLine = true)
    {
        require_once 'WpOption/WpCheckBoxOption.php';
        $spigaOption = new WpCheckBoxOption($name, $selectedValues, $onePerLine);
        $spigaOption->setTitle($title);
        $spigaOption->setDescription($description);
        $spigaOption->setOptions($options);
        $this->options[$name] = $spigaOption;
    }
    
    /**
     * Agrega una opci�n de tipo checkbox que regresa un boleano
     *
     * @param string $name
     * @param mixed $defaultValue
     * @param string [optional] $title
     * @param string [optional] $description
     * @access public
     */
    public function addCheckOption($name, $defaultValue, $title = '', $description = '')
    {
        require_once 'WpOption/WpCheckOption.php';
        $spigaOption = new WpCheckOption($name, $defaultValue);
        $spigaOption->setTitle($title);
        $spigaOption->setDescription($description);
        $this->options[$name] = $spigaOption;
    }
    
    /**
     * Agrega un dropdown/pulldown/combobox como quieras llamarle
     *
     * @param string $name
     * @param int|string $selectedValue
     * @param mixed $options Array asociativo con los valores a mostrar
     * @param string [optional] $title
     * @param string [optional] $description
     * @access public
     */
    public function addSelectOption($name, $options, $selectedValue, $title = '', $description = '')
    {
        require_once 'WpOption/WpSelectOption.php';
        $spigaOption = new WpSelectOption($name, $selectedValue);
        $spigaOption->setTitle($title);
        $spigaOption->setDescription($description);
        $spigaOption->setOptions($options);
        $this->options[$name] = $spigaOption;
    }
    
    /**
     * Agrega un dropdown/pulldown/combobox como quieras llamarle de selecci�n m�ltiple
     *
     * @param string $name
     * @param mixed $selectedValues
     * @param mixed $options Array asociativo con los valores a mostrar
     * @param string [optional] $title
     * @param string [optional] $description
     * @access public
     */
    public function addMultipleSelectOption($name, $options, $selectedValues, $title = '', $description = '')
    {
        require_once 'WpOption/WpMultipleSelectOption.php';
        $spigaOption = new WpMultipleSelectOption($name, $selectedValues);
        $spigaOption->setTitle($title);
        $spigaOption->setDescription($description);
        $spigaOption->setOptions($options);
        $this->options[$name] = $spigaOption;
    }
    
    /**
     * Agrega un dropdown/pulldown/combobox que lista las categorias
     *
     * @param string $name
     * @param int|mixed [optional] $selectedValue Si el campo ser� de opci�n multiple, 
     *   se necesita enviar un arreglo en caso contrario se envia un entero
     * @param boolean [optional] $isMultiple 
     * @param string [optional] $title
     * @param string [optional] $description
     * @access public
     */
    public function addSelectCategoriesOption($name, $selectedValue = 0, $isMultiple = false, $title = '', $description = '')
    {
        require_once 'WpOption/WpSelectCategoriesOption.php';
        $spigaOption = new WpSelectCategoriesOption($name, $selectedValue);
        $spigaOption->setTitle($title);
        $spigaOption->setDescription($description);
        $spigaOption->setIsMultiple($isMultiple);
        $this->options[$name] = $spigaOption;
    }
    
    /**
     * Agrega un dropdown/pulldown/combobox que lista las p�ginas
     *
     * @param string $name
     * @param int|mixed [optional] $selectedValue Si el campo ser� de opci�n multiple, 
     *   se necesita enviar un arreglo en caso contrario se envia un entero
     * @param boolean [optional] $isMultiple 
     * @param string [optional] $title
     * @param string [optional] $description
     * @access public
     */
    public function addSelectPagesOption($name, $selectedValue = 0, $isMultiple = false, $title = '', $description = '')
    {
        require_once 'WpOption/WpSelectPagesOption.php';
        $spigaOption = new WpSelectPagesOption($name, $selectedValue);
        $spigaOption->setTitle($title);
        $spigaOption->setDescription($description);
        $spigaOption->setIsMultiple($isMultiple);
        $this->options[$name] = $spigaOption;
    }
    
    /**
     * Agrega un dropdown/pulldown/combobox que lista los usuarios del blog
     *
     * @param string $name
     * @param int|mixed [optional] $selectedValue Si el campo ser� de opci�n multiple, 
     *   se necesita enviar un arreglo en caso contrario se envia un entero
     * @param boolean [optional] $isMultiple 
     * @param string [optional] $title
     * @param string [optional] $description
     * @access public
     */
    public function addSelectUsersOption($name, $selectedValue = 0, $isMultiple = false, $title = '', $description = '')
    {
        require_once 'WpOption/WpSelectUsersOption.php';
        $spigaOption = new WpSelectUsersOption($name, $selectedValue);
        $spigaOption->setTitle($title);
        $spigaOption->setDescription($description);
        $spigaOption->setIsMultiple($isMultiple);
        $this->options[$name] = $spigaOption;
    }
    
    /**
     * Agrega un dropdown/pulldown/combobox que lista los tags
     *
     * @param string $name
     * @param int|mixed [optional] $selectedValue Si el campo ser� de opci�n multiple, 
     *   se necesita enviar un arreglo en caso contrario se envia un entero
     * @param boolean [optional] $isMultiple 
     * @param string [optional] $title
     * @param string [optional] $description
     * @access public
     */
    public function addSelectTagsOption($name, $selectedValue = 0, $isMultiple = false, $title = '', $description = '')
    {
        require_once 'WpOption/WpSelectTagsOption.php';
        $spigaOption = new WpSelectTagsOption($name, $selectedValue);
        $spigaOption->setTitle($title);
        $spigaOption->setDescription($description);
        $spigaOption->setIsMultiple($isMultiple);
        $this->options[$name] = $spigaOption;
    }
    
    /**
     * Agrega una opci�n de tipo String (input)
     *
     * @param string $name
     * @param string $defaultValue
     * @param string [optional] $title
     * @param string [optional] $description
     * @access public
     */
    public function addDatePickerOption($name, $defaultValue, $title = '', $description = '')
    {
        require_once 'WpOption/WpDatePickerOption.php';
        $spigaOption = new WpDatePickerOption($name, $defaultValue);
        $spigaOption->setTitle($title);
        $spigaOption->setDescription($description);
        $this->options[$name] = $spigaOption;
    }
    
    /**
     * Envia a pantalla el m�todo __toString y adem�s checa los cambios que se realizaron en los valores
     * @access public
     */
    public function render()
    {
        $this->saveTemplates();
        $this->updateOptions();
        echo $this->__toString();
    }
    
    /**
     * Genera el formulario (metabox) de las opciones agregadas
     * @access public
     */
    public function renderMetaBox()
    {
        global $post;
        $this->saveTemplates();
        $fields = '';
        foreach ( $this->optionsInMetaBox as $option )
        {
            if ($option->getRequire() != null)
            {
                $this->options[$option->getRequire()]->setInputName($this->getCamelCase('wp_options_' . $this->baseThemeName));
                $this->options[$option->getRequire()]->setDbSource(WpOption::$Sources['OPTION']);
                if ($this->options[$option->getRequire()]->getValue() == false)
                    continue;
            }
            $option->setDbSource(WpOption::$Sources['POST_META']);
            $option->setTemplate($this->templateOption);
            $option->setDefaultValue('');
            $option->setValue('');
            $option->setPost($post);
            $fields .= $option;
        }
        $this->templateLayoutMetaBox = str_replace('%fields%', $fields, $this->templateLayoutMetaBox);
        echo $this->templateLayoutMetaBox;
    }
    
    /**
     * Guarda la metadata del post, (metabox)
     * @param int $idPost
     * @access public
     */
    public function savePostData($idPost)
    {
        if (isset($_POST['post_type']) && $_POST['post_type'] == 'page')
        {
            if (! current_user_can('edit_page', $idPost))
            {
                throw new Exception('Usted no tiene permisos para editar la p�gina');
            } else if (! current_user_can('edit_post', $idPost))
            {
                throw new Exception('Usted no tiene permisos para editar el post');
            }
        }
        foreach ( $this->optionsInMetaBox as $option )
        {

            $option->setDbSource(WpOption::$Sources['POST_META']);
            $option->setInputName($this->getCamelCase('wp_options_' . $this->baseThemeName));
            $option->setDefaultValue('');
            $option->setValue('');
            if(is_array($_POST[$option->getFormName()]))
                $data = serialize($_POST[$option->getFormName()]);
            else
                $data = (get_magic_quotes_gpc()) ? stripslashes($_POST[$option->getFormName()]) : $_POST[$option->getFormName()]; 
            $data = $option->set($data);
            
            if (get_post_meta($idPost, $option->getName() . '_value') == "")
                add_post_meta($idPost, $option->getName() . '_value', $data, true);
            elseif ($data != get_post_meta($idPost, $option->getName() . '_value', true))
                update_post_meta($idPost, $option->getName() . '_value', $data);
            elseif ($data == "")
                delete_post_meta($idPost, $option->getName() . '_value', get_post_meta($idPost, $option->getName() . '_value', true));
              
        }
    }
    
    /**
     * Muestra la pagina de opciones
     * @return string
     * @access public
     */
    public function __toString()
    {
        $this->addContent("<script type='text/javascript' src='{$this->themeLocation}/lib/js/jquery-1.3.js'></script>\n");
        $this->addContent("<script type='text/javascript' src='{$this->themeLocation}/lib/js/jquery.ui.all.js'></script>\n");
        $this->addContent("<script type='text/javascript' src='{$this->themeLocation}/lib/js/actions.js'></script>\n");
        $this->addCSS('ui.all');
        
        if (count($this->css) > 0)
            $this->includeStyles();
        
        $fields = $this->getChilds($this->options, '__root__');
        $this->templateLayout = str_replace('%fields%', $fields, $this->templateLayout);
        $this->templateLayout = str_replace('%updatedMessage%', ($this->updated ? "<div class='updated'><p><b>" . _('Updated Options') . "</b></p></div>\n" : ''), $this->templateLayout);
        $this->addContent($this->templateLayout);
        return $this->content;
    }
    
    /**
     * Muesta las opciones que son dependientes de una opci�n
     *
     * @param mixed $options
     * @param string $parentName
     * @return string
     * @access private
     */
    private function getChilds($options, $parentName)
    {
        $fields = '';
        foreach ( $options as $option )
        {
            if (is_subclass_of($option, 'WpOption'))
            {
                if ($option->getHideInOptions())
                    continue;
                if ($option->getParent() != $parentName)
                    continue;
                
                $option->setInputName($this->getCamelCase('wp_options_' . $this->baseThemeName));
                $option->setTemplate($this->templateOption);
                $option->setDbSource(WpOption::$Sources['OPTION']);
                if ($parentName != '__root__' && $this->options[$parentName]->getValue() == false)
                    $option->setVisible(false);
            }
            if (get_class($option) == 'WpOptionTitle')
                $option->setTemplate($this->templateHeader);
            $fields .= ($option->__toString());
            
            if (is_subclass_of($option, 'WpOption') && $option->hasChilds())
                $fields .= (string) $this->getChilds($option->getChilds(), $option->getName());
        }
        return $fields;
    }
    
    /**
     * Guarda los nuevos valores del plugin
     * dependiendo de la interaccion del usuario
     * @access public
     */
    public function updateOptions()
    {
        if (isset($_POST['post']) && $_POST['post'] == 'updateWpOptions')
        {
            $prefix = $this->getCamelCase('wp_options_' . $this->baseThemeName);
            foreach ( $this->options as $optionName => $option )
            {
                if (is_subclass_of($option, 'WpOption'))
                {
                    $value = (is_string($_POST[$prefix][$optionName])) ? stripslashes($_POST[$prefix][$optionName]) : $_POST[$prefix][$optionName]; 
                    update_option($prefix . '_' . $optionName, $option->set( $value ));
                }
            }
            $this->updated = true;
        }
        if (isset($_POST['post']) && $_POST['post'] == 'deleteWpOptions')
        {
            foreach ( $this->options as $optionName => $option )
            {
                if (is_subclass_of($option, 'WpOption'))
                {
                    delete_option($this->getCamelCase('wp_options_' . $this->themeName) . '_' . $optionName);
                }
            }
            update_option('current_theme', 'default');
            update_option('template', 'default');
            update_option('stylesheet', 'default');
            do_action('switch_theme', 'Default');
            print '<meta http-equiv="refresh" content="0;URL=themes.php?activated=true">';
            echo "<script> self.location(\"themes.php?activated=true\");</script>";
            exit();
        }
    }
    
    /**
     * Regresa el valor de una opci�n almacenada
     * @access public
     */
    public function getOption($optionName)
    {
        if (! isset($this->options[$optionName]))
            throw new Exception(_("The option {$optionName} doesn't exists"));
        $this->options[$optionName]->setInputName($this->getCamelCase('wp_options_' . $this->baseThemeName));
        return $this->options[$optionName]->getValue();
    }
    
    /**
     * Regresa el valor de una opci�n almacenada en el post
     * @access public
     */
    public function getPostOption($optionName)
    {
        global $post;
        if (! isset($this->options[$optionName]))
            throw new Exception(_("The option {$optionName} doesn't exists"));
        $this->options[$optionName]->setInputName($this->getCamelCase('wp_options_' . $this->baseThemeName));
        $this->options[$optionName]->setPost($post);
        $this->options[$optionName]->setDbSource(WpOption::$Sources['POST_META']);
        return $this->options[$optionName]->getStoredValue();
    }
    
    /**	
     * Genera los tags para agregar las hojas de estilo
     * @access private
     */
    private function includeStyles()
    {
        foreach ( $this->css as $css )
        {
            $this->content .= "<link rel='stylesheet' href='{$this->themeLocation}/lib/css/{$css}.css' type='text/css' media='all' />";
        }
    }
    
    /**
     * CSS filePath
     * @param string $css
     * @access public
     */
    public function addCSS($css)
    {
        array_push($this->css, $css);
    }
    
    /**
     * @return string
     * @access public
     */
    public function getThemeName()
    {
        return $this->themeName;
    }
    
    /**
     * @param string $themeName
     * @access public
     */
    public function setThemeName($themeName)
    {
        $this->themeName = $themeName;
        $this->baseThemeName = ereg_replace("[^A-Za-z0-9\\_\\ ]", "", $themeName);
    }
    
    /**
     * @param string $themeLocation
     * @access public
     */
    public function setThemeLocation($themeLocation)
    {
        $this->themeLocation = get_bloginfo('siteurl') . $themeLocation;
    }
    
    /**
     * Agrega contenido a la vista
     *
     * @param string $content
     * @access private
     */
    private function addContent($content)
    {
        $this->content .= $content;
    }
    
    /**
     * Regresa el CamelCase de una cadena de caracteres separada por guion bajo 
     *
     * @param string $string
     * @param boolean $first
     * @param boolean $preserve
     * @return string
     * @access private
     */
    private function getCamelCase($string, $first = false, $preserve = false)
    {
        $string = str_replace(" ", '_', $string);
        $array = explode('_', $string);
        $string = '';
        foreach ( $array as $i => $segment )
        {
            if (! $preserve)
                $segment = strtolower($segment);
            if ($i || $first)
                $segment = ucfirst($segment);
            $string .= $segment;
        }
        return $string;
    }
    
    /**
     * @return string
     * @access public
     */
    public function getForumUrl()
    {
        return $this->forumUrl;
    }
    
    /**
     * @return string
     * @access public
     */
    public function getManualUrl()
    {
        return $this->manualUrl;
    }
    
    /**
     * @param string $forumUrl
     * @access public
     */
    public function setForumUrl($forumUrl)
    {
        $this->forumUrl = $forumUrl;
    }
    
    /**
     * @param string $manualUrl
     * @access public
     */
    public function setManualUrl($manualUrl)
    {
        $this->manualUrl = $manualUrl;
    }
    
    /**
     * Variable que almacena el layout de las opciones en el formulario
     * @var string
     * @access private
     */
    private $templateOption = "";
    
    /**
     * Variable que almacena el layout para el dise�o de los headers
     * @var string
     * @access private
     */
    private $templateHeader = "";
    
    /**
     * Variable que almacena el layout para el dise�o en general
     * @var string
     * @access private
     */
    private $templateLayout = '';
    
    /**
     * El dise�o del metabox
     *
     * @var string
     * @access private
     */
    private $templateLayoutMetaBox = '';
    
    /**
     * Guarda el template a utilizar en los headers
     * @access private
     */
    private function saveTemplates()
    {
        $this->templateHeader = <<<TPL

			<tr valign="top">
				<th colspan="2" style="background-image:url(images/menu-bits.gif); background-color:#7F7F7F; color:#FFF; margin:0; padding:5px 0 5px 10px; font:normal 13px/18px Georgia, Times New Roman, Times, serif;">
					%title%
				</th>
			</tr>	
TPL;
        $this->templateOption = <<<TPL

			<tr%visible% class="%class%">
				<td style='background:#F7F7F7; border-right:1px solid #F0F0F0; font-weight:bold; text-align:right;' >%title%</td>
				<td>%input% %description%</td>
			</tr>
TPL;
        $this->templateLayout = <<<TPL

			<div class="wrap">
				<div class="icon32" id="icon-tools"><br /></div>
				<a style="text-decoration:none; margin:10px 20px 0 0; border:none; float:right;" href="http://storelicious.com" title="Pro Themes"><img src="{$this->themeLocation}/lib/pix/brandstorelicious.gif" alt="Storelicious" /> </a>
				<h2>Welcome to configuration page of <strong>{$this->themeName}</strong>!</h2>
				%updatedMessage%
				
				<form action=""" method="post" style="margin:20px 0 0 0;">
				
				<div style="clear:both;height:20px;"></div>
    				<div class="info">
      				<div style="width: 70%; float: left; display: inline;padding-top:4px;">
      					<strong>Stuck on these options?</strong> <a href="{$this->manualUrl}" target="_blank">Read The Documentation Here</a> or 
      					<a href="{$this->forumUrl}" target="blank">Visit Our Support Forum</a></div>
					<div style="width: 30%; float: right; display: inline;text-align: right;">
        				<input name="save" class="button-primary" type="submit" value="Save changes" />
      				</div>
     				 <div style="clear:both;"></div>
    			</div>
				
				
				
					<input type="hidden" name="post" value="updateWpOptions">
					<table class="widefat" id="storelicious">
						<thead><tr><th colspan="2">{$this->themeName}</th></tr></thead>
						<tbody>%fields%</tbody>							
					</table>
					<p class="submit"><input type="submit" class="button-primary" value="Save changes" />
				</form>
				<h2>Delete Theme options</h2>
				<p>To completely remove these theme options from your database (reminder: they are all stored in Wordpress options table <em>{$this->wpdb->options}</em>), click on
				the following button. You will be then redirected to the <a href="themes.php">Themes admin interface</a> and the Default theme will have been activated.</p>
				<p><strong>Special notice for people allowing their readers to change theme</strong> (i.e. using a Theme Switcher on their blog)<br/>
				Unless you really remove the theme files from your server, this theme will still be available to users, and therefore will self-install again as soon as someone selects it. Also, all custom variables as defined in the above menu will be blank, this could lead to unexpected behaviour.
				Press "Delete" only if you intend to remove the theme files right after this.</p>
				<form action="" method="post">
					<input type="hidden" name="post" value="deleteWpOptions" />
					<p class="submit"><input type="submit" value="Delete Options" onclick="return confirm('Are you really sure you want to delete ?');"/></p>
				</form>
			</div>
TPL;
        
        $this->templateLayoutMetaBox = <<<TPL
		
				<table class="widefat">
					<tbody>
						%fields%
					</tbody>							
				</table>
			
TPL;
    }

}

















