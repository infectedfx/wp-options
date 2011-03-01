<?php

if(!defined('THEME_OPTIONS_ROOT'))
    define('THEME_OPTIONS_ROOT',dirname(__FILE__).'/');

include THEME_OPTIONS_ROOT."options/theme-options-view.php";

// Redirect to Theme Options after Activation

if (is_admin() && isset($_GET['activated'] ) && "themes.php" == $pagenow) {
    header( 'Location: '.admin_url().'admin.php?page=storelicious' ) ;
}

global $_wpo;
$_wpo = array();

load_theme_textdomain('storelicious',get_template_directory().'/lang');
function _s($string, $namespace = 'storelicious')
{
    return __($string,$namespace);
}


function add_theme_options_page()
{
	global $_wpo;
    if(function_exists('add_object_page'))
    {
        add_object_page(_s('Configure ') . $_wpo['name'], $_wpo['name'], 'edit_themes', 'storelicious',  'render_options_page',  $_wpo['icon']);
    }
    else
    {
        add_menu_page(_s('Configure ') . $_wpo['name'], $_wpo['name'], 'edit_themes', 'storelicious',  'render_options_page',  $_wpo['icon']);
    }
    /*
    // TODO manager de las metabox
    foreach($this->subpages as $sub)
    {
        add_submenu_page(basename(__FILE__), _s($sub['pageTitle']), _s($sub['title']), 'edit_themes', $sub['slug'], $sub['function']);
    }
    
    if ($this->hasMetaBox())
    {
        add_meta_box('wpoptions_section', $this->themeName . ' :: '._s("Post Settings"), $this->getFunctionScope('renderMetaBox'), 'post', 'normal','high',array('type'=>'post'));
        add_meta_box('wpoptions_section', $this->themeName . ' :: '._s("Post Settings"), $this->getFunctionScope('renderMetaBox'), 'page', 'normal','high',array('type'=>'page'));
        add_action('save_post', $this->getFunctionScope('savePostData'));
    }*/
}
add_action('admin_menu', 'add_theme_options_page');


function setup_options($manual_url, $forum_url, $home_url, $options, $icon = null, $more = null)
{
	global $_wpo;
	$info = get_theme_data( get_template_directory().'/style.css' );
	$_wpo['name'] = $info['Name'];
	$_wpo['author'] = $info['Author'];
	$_wpo['more'] = $more ? $more : "http://storelicious.com/themes";
	$_wpo['version'] = $info['Version'];
	$_wpo['title'] = $info['Title'];
	$_wpo['manual'] = $manual_url;
	$_wpo['forum'] = $forum_url;
	$_wpo['home'] = $home_url;
	$_wpo['icon'] = $icon ? $icon : get_bloginfo('template_url').'/lib/assets/pix/panel/storelicious-icon.png';
	$_wpo['fversion'] = get_theme_options_version();
	$_wpo['options'] = $options;
}

function update_storelicious_options()
{
	global $_wpo;
	$updated = false;
	if (isset($_POST['storelicious-post']) && $_POST['storelicious-post'] == 'storelicious-post')
	{
		if (! wp_verify_nonce($_POST['_wpnonce'], 'update-wp-options') ) wp_die(_s("Security check"));
		foreach($_wpo['options'] as $key => $option)
		{
			if(is_array($option['type']))
			{
				foreach ($option['type'] as $child)
					update_storelicious_option($child);
			}
			else
				update_storelicious_option($option);
		}
		$updated = true;
	}
	return $updated;
}

function update_storelicious_option($option)
{
	$id = $option['id'];
	$value = (is_string($_POST[$id])) ? stripslashes($_POST[$id]) : $_POST[$id];
	update_option($id,$value); 
}

function get_theme_options_version()
{
	return include THEME_OPTIONS_ROOT."version.php";
}


function get_theme_option()
{
}

function set_theme_option()
{
}




