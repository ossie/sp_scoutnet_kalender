<?php
/*******************************************************************************
 With the included buttonsnap class library, you can create buttons in the 
 editor toolbars of both the standard editor and the wysiwyg editor using the
 same commands.  You do not need to test which editor is being used, nor do you
 need to provide separate code for both editors.
 
 To add additional buttons to the toolbars in the WordPress editors, you must
 include the buttonsnap.php file as the first executable line in your plugin:
 
    include('buttonsnap.php');
    
 You may distribute the buttonsnap.php file with your plugin.  Put it in the
 same directory as your plugin file.  Multiple plugins may use the 
 buttonsnap.php code, even if copies reside in different directories.
 
 It is possible to distribute your plugin without the buttonsnap.php file.
 Copy and paste the entire contents of the buttonsnap.php file to the top of 
 your plugin (just under the header is fine).  The only caveat is that your 
 plugin will not be able to create Ajax buttons in this configuration.  This 
 will not affect the functionality of Ajax buttons in other plugins.
 
 Note for your support that the buttonsnap class is a singleton.  If someone
 using your plugin is using anohter plugin that uses buttonsnap, only one of
 the buttonsnap classes will be active.  If theirs is out of date, it is 
 possible that it will not contain certain features upon which your plugin is
 dependent.  When troubleshooting, be sure to verify that all plugins have the 
 latest version of buttonsnap available.
 
 To create a new button, call the appropriate helper functions-
 
 To create a button that replaces the selected text with specific text:
 
    buttonsnap_textbutton( BUTTON_IMAGE_URL, BUTTON_TEXT, REPLACEMENT_TEXT);
    
 To create a button that executes your own javascript:
 
    buttonsnap_jsbutton( BUTTON_IMAGE_URL, BUTTON_TEXT, JAVASCRIPT_STRING);
    
 To create a button that replaces the selected text with the result of a 
 WordPress filter:
 
    buttonsnap_ajaxbutton( BUTTON_IMAGE_URL, BUTTON_TEXT, WORPRESS_PLUGIN_HOOK);
    
 You should sink the hook used in buttonsnap_ajaxbutton() so that the value
 returned is the value that is used to replace the selected text.  The selected
 text is passed in as a parameter to the sink function.
 
 What follows is an example plugin that uses the methods described above to 
 create one of each type of button.
 
 Icons were provided by http://www.famfamfam.com/lab/icons/silk/ under
 a Creative Commons Attribution 2.5 license.

*******************************************************************************/
 
include('buttonsnap.php');
 
add_action('init', 'sp_ScoutnetKalender_Button');

// A custom plugin hook for adding CSS to the TinyMCE Editor when markers are registered
//add_action('marker_css', 'my_button_marker_css');

function sp_ScoutnetKalender_Button()
{
	// Set up some unique button image URLs for the new buttons from the plugin directory
	$kalender_button = buttonsnap_dirname(__FILE__) . '/bilder/ical_einzeln.png';
	// Calling buttonsnsap_dirname() with a filename returns the URL of that file's directory
	// Calling buttonsnsap_dirname() without a filename returns the URL of the active buttonsnap class' directory

	// Create a vertical separator in the WYSI toolbar (does nothing in the Quicktags):
	//buttonsnap_separator();

	// Create a button that changes the selected text to something specific:
	//buttonsnap_textbutton($button_image_url1, 'Text Button', 'Help?<br/><!--help-->');
	
	// Register an image marker to display in the RTE instead of an invisible comment:
	//buttonsnap_register_marker('help', 'ebt_marker');
	//   'help' is the text that will be replaced with an image in the RTE.
	//   'ebt_marker' is the CSS class that is used to display the marker in the RTE.
	//   The my_button_marker_css() hook sink adds CSS to display a graphic for that selector class.
	
	// Create a button that executes the provided javascript:
	$kalender_JS = 'window.open("'.buttonsnap_dirname(__FILE__).'/sp_buttonsnap-popup.php", "ScoutnetKalender",  "width=470,height=230,scrollbars=yes");';
	buttonsnap_jsbutton($kalender_button, 'Termin oder Kalender einfügen', $kalender_JS);
	
	// Create a button that uses Ajax to fetch replacement text from a WordPress plugin hook sink:
	//buttonsnap_ajaxbutton($button_image_url3, 'Ajax Button', 'my_hook');
	//add_filter('my_hook', 'my_hook_sink');
}

/*function my_hook_sink($selectedtext)
{
	return 'Timestamp: ' . date('Y-m-d H:i:s');
}

function my_button_marker_css()
{
	$marker_image_url = buttonsnap_dirname(__FILE__) . '/help_brick.gif';
	echo "
		.ebt_marker {
				border: 0px;
				border-top: 1px dotted #cccccc;
				display:block;
				background-color: #ffffff;
				margin-top:15px;
				background-image: url({$marker_image_url});
				background-repeat: no-repeat;
				background-position: right top;
		}
	";
}*/

?>