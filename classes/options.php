<?php
namespace MDT;

class options
{
	const pluginname = "mntdbtable";
	function init()
	{
		// create custom plugin settings menu
		add_action('admin_menu', array($this,'settings') );
	}

	function params()
	{
		$params = array 
		(
			(object) array("label"=>"organisation","name"=>"mdt_organisation", "type"=>"text", "default"=>""),
			(object) array("label"=>"selectmaxlines","name"=>"mdt_selectmaxlines", "type"=>"text", "default"=>"5,10,20,30,50"),
		);
		return($params);
	}

	function settings() 
	{

		//create new top-level menu
		$self = new self();
		$name=$self::pluginname;
		add_menu_page($name . ' settings', $name , 'administrator', __FILE__, array($this,'settings_page') , plugins_url('/images/icon.png', __FILE__) );

	//call register settings function
		add_action( 'admin_init', array($this,'register_settings') );
	}


	function register_settings() 
	{
		$params = $this->params();
		foreach ($params as $param)
		{
			$name = '' . $param->name;
			register_setting( 'settings-group', $name );
		}
	}

	function settings_page()
	{
		$form = '';
		$form .= '<div class="wrap">';
		$form .= '<h1>' . $this->pluginname . '</h1>';

		$form .= '<form method="post" action="options.php">';
		echo $form;
		settings_fields( 'settings-group' );
		do_settings_sections( 'settings-group' );
		
		$form = '';
		$form .= '<table class="form-table">';
		
		$params = $this->params();
		foreach ($params as $param)
		{
			$form .= '<tr valign="top">';
			$form .= '<th scope="row">' . $param->label . '</th>';
			$name = "" . $param->name;
			if($param->type == "radio")
			{
				$value = esc_attr(get_option($name));
				$form .= '<td>';
				foreach($param->options as $option)
				{
					$selected="";
					if($value == $option) { $selected = " checked";}
					$form .= '<input type="' . $param->type . '" name="' . $name . '" value="' . $option . '"' . $selected . '/>' . $option . '<br>';
				}
				$form .= '</td>';
			}
			else
			{
				$value = esc_attr(get_option($name));
				if(!$value) { $value = $param->default;}
				$form .= '<td><input type="' . $param->type . '" name="' . $name . '" value="' . $value . '" /></td>';
			}
			$form .= '</tr>';
		}
		$form .= '</table>';
		echo $form;
		submit_button();
	
		$form = '';
		$form .= '</form>';
		$form .= '</div>';
		echo $form;
	}
}

?>