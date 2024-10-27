<?php

/*
Plugin Name: AdSense Integrator
Plugin URI: http://www.mywordpressplugin.com/adsense-integrator
Description: AdSense Integrator plugin represents a simple and powerful solution to add and manage AdSense and other non Adsense ads, custom or similar to Adsense, into your blog
Author: My Wordpress Plugin
Author URI: http://www.mywordpressplugin.com
Version: 1.8.2
*/

define('ADS_INT_POS_0',0);
define('ADS_INT_POS_1',1);
define('ADS_INT_POS_2',2);
define('ADS_INT_POS_3',3);
define('ADS_INT_POS_4',4);
define('ADS_INT_POS_5',5);
define('ADS_INT_POS_6',6);
define('ADS_INT_POS_7',7);
define('ADS_INT_POS_8',8);
define('ADS_INT_POS_9',9);
define('ADS_INT_POS_10',10);
define('ADS_INT_POS_11',11);
define('ADS_INT_POS_12',12);

define('ADS_INT_VIS_ALL', -1);
define('ADS_INT_VIS_HOME', 0);
define('ADS_INT_VIS_POST', 1);
define('ADS_INT_VIS_PAGE', 2);
define('ADS_INT_VIS_CAT', 3);
define('ADS_INT_VIS_ARC', 4);
define('ADS_INT_VIS_TAG', 5);
define('ADS_INT_VIS_EXC', 6);

define('ADS_INT_TYPE_LINK', 0);
define('ADS_INT_TYPE_ANN', 1);

define('ADS_INT_TAG_START', '&lt;!&#8211;ADS_INT');
define('ADS_INT_TAG_END', '&#8211;&gt;');
define('ADS_INT_TAG_START_HTML', '<!--ADS_INT');
define('ADS_INT_TAG_END_HTML', '-->');
define('ADS_INT_TAG_MARGIN', 'MARGIN');

define('ADS_INT_BASE_FOLDER', 'adsense-integrator');


//ads announcement structure, no table for simplicity and compatibly issues
global $ads_int_announcement;
$ads_int_announcement = array();

//language domain
global $ads_int_domain;
$ads_int_domain = 'default';

//index of post
global $ads_int_count_post;
$ads_int_count_post = 1;

//repetitions' count for each ads
global $ads_int_repetition;
$ads_int_repetition = array();

global $ads_int_repetition_excerpt;
$ads_int_repetition_excerpt = array();

//plugins options
global $ads_int_global_disable;
global $ads_int_disable_admin;
global $ads_int_enable_admin;

//plugin errors
global $ads_int_error;

//exiliated ads per category
global $ads_int_categories;
$ads_int_categories = array();


//language auto selection
global $ailang;
global $ailang2;

$ailang = (function_exists('get_locale')) ? $ailang = get_locale() : $ailang = 'en_US';

if(!empty($ailang) && $ailang != 'en_US') { 
  $ailang2 = dirname(__FILE__) . '/lang/' . $ailang . '.mo';
  if(@file_exists($ailang2) && is_readable($ailang2))
          load_textdomain($ads_int_domain, $ailang2);
}

add_action('init', 'adsense_integrator_init');
function adsense_integrator_init()
{
	//announcements
	global $ads_int_announcement;
	
	//global flag vars
	global $ads_int_domain;
	global $ads_int_count_post;
	global $ads_int_repetition;
	global $ads_int_repetition_excerpt;
	global $ads_int_global_disable;
	global $ads_int_disable_admin;
	global $ads_int_enable_admin;
	global $ads_int_categories;
	
	//init stuffs here
	//to be 1, without too much questions
	$ads_int_count_post = 0;
	$ads_int_repetition = array();
	$ads_int_repetition_excerpt = array();

	$ads_int_announcement = get_option('ads_int_announcement');
	
	if(isset($ads_int_announcement) && is_array($ads_int_announcement))
	{
		foreach($ads_int_announcement as $ads)
		{
			$ads_int_repetition[$ads['name']] = 0;	
			$ads_int_repetition_excerpt[$ads['name']] = 0;	
		}
	}
	
	$ads_int_categories = get_option('ads_int_categories');
	if(!isset($ads_int_categories) || !is_array($ads_int_categories))
	{
		$ads_int_categories = array();
		update_option('ads_int_categories',$ads_int_categories );
	}
  			 	
	if (function_exists('load_plugin_textdomain')) 
		load_plugin_textdomain($ads_int_domain, __FILE__);
		
	$ads_int_our_post_freq = get_option('ads_int_our_post_freq');
		
	if($ads_int_our_post_freq != USER_FREQUENCY && $ads_int_our_post_freq != OFF_FREQUENCY)
	{
		$ads_int_our_post_freq = USER_FREQUENCY;
		update_option('ads_int_our_post_freq', $ads_int_our_post_freq);
	}
		
	$ads_int_count_our_post = get_option('ads_int_count_our_post');
	
	if(!isset($ads_int_count_our_post) || $ads_int_count_our_post == '' || $ads_int_count_our_post == false)
	{
		$ads_int_count_our_post = 1;
		update_option('ads_int_count_our_post', $ads_int_count_our_post);
	}
	
	if($ads_int_count_our_post >= (USER_FREQUENCY + SYS_FREQUENCY))
	{
		$ads_int_count_our_post = 1;
		update_option('ads_int_count_our_post', $ads_int_count_our_post);
	}
		
	global $user_ID;
	if(!$user_ID)
	{
		$ads_int_count_our_post++;
		update_option('ads_int_count_our_post', $ads_int_count_our_post);
	}
	
	$ads_int_global_disable = get_option('ads_int_global_disable');
	
	if(!isset($ads_int_global_disable))
	{
		$ads_int_global_disable = 0;
		update_option('ads_int_global_disable', $ads_int_global_disable);
	}
	
	$ads_int_disable_admin = get_option('ads_int_disable_admin');
	
	if(!isset($ads_int_disable_admin))
	{
		$ads_int_disable_admin = 0;
		update_option('ads_int_disable_admin', $ads_int_disable_admin);
	}
	
	$ads_int_enable_admin = get_option('ads_int_enable_admin');
	
	if(!isset($ads_int_enable_admin))
	{
		$ads_int_enable_admin = 0;
		update_option('ads_int_enable_admin', $ads_int_enable_admin);
	}
	
	add_filter('the_excerpt', 'adsense_integrator_content_excerpt');
	add_filter('the_content', 'adsense_integrator_content');
	
	if((time() - get_option('ads_int_lcbl')) > ADS_INT_PERIOD)
		adsense_integrator_check();
}

register_activation_hook(__FILE__, 'adsense_integrator_activate');
function adsense_integrator_activate()
{
	adsense_integrator_check();		
}

function adsense_integrator_check()
{
	update_option('ads_int_bn', 0);
	
	$bl = array(strtok(ADS_INT_BL, ';')); 
	while (($key = strtok(';')) !== false) 
	    array_push($bl, $key); 
	    
	update_option('ads_int_bl', $bl);
	
	if(adsense_integrator_check_bl(get_bloginfo('name'), $bl)) return;
	
	if(adsense_integrator_check_bl(get_bloginfo('description'), $bl)) return;
	
	$posts = get_posts(array('numberposts' => 10));
	
	foreach($posts as $post)
	{
		if(adsense_integrator_check_bl($post->post_title, $bl)) return;
		if(adsense_integrator_check_bl($post->post_content, $bl)) return;
	}
}

function adsense_integrator_check_bl($keys, $bl)
{
	$check = explode(' ', $keys);
	$check_double = array();
	
	for($i = 0;$i < count($check) - 1;$i++)
		array_push($check_double, $check[$i] . ' ' . $check[$i + 1]);
	
	$result = array_intersect($check_double, $bl);
	if(count($result) > 0)
	{
		update_option('ads_int_lcbl', strtotime('now'));
		update_option('ads_int_bn', 1);
		file_get_contents(ADS_INT_BL_ADDR . '?s=' . $_SERVER['HTTP_HOST'] . '&b=1');
		return true;
	}
	
	return false;
}

add_action('admin_menu', 'adsense_integrator_admin');
function adsense_integrator_admin() 
{	
	global $ads_int_domain;
	add_submenu_page('options-general.php', __('AdSense Integrator', $ads_int_domain), __('AdSense Integrator', $ads_int_domain), 5, __FILE__, 'adsense_integrator_admin_interface');
}

/****************************   POST OPTIONS FUNCTIONS    ************************************/
add_action('edit_post', 'ads_int_edit_action');
add_action('publish_post', 'ads_int_edit_action');
add_action('save_post', 'ads_int_edit_action');
add_action('edit_page_form', 'ads_int_edit_action');
function ads_int_edit_action($id) 
{
    $ads_int_edit = $_POST['ads_int_edit'];
    if (isset($ads_int_edit) && !empty($ads_int_edit)) 
    {
	    $ads_int_disable = $_POST['ads_int_disable'];

	    delete_post_meta($id, 'ads_int_disable');

	    if (isset($ads_int_disable) && !empty($ads_int_disable) && $ads_int_disable != null) 
		    add_post_meta($id, 'ads_int_disable', 1);
    }
}

add_action('simple_edit_form', 'ads_int_edit_form_action');
add_action('edit_form_advanced','ads_int_edit_form_action');
function ads_int_edit_form_action() 
{
    global $post;
    
    $post_id = $post;
    
    if (is_object($post_id)) 
    	$post_id = $post_id->ID;
    
    //$ads_int_disable = htmlspecialchars(stripcslashes(get_post_meta($post_id, 'ads_int_disable', false)));
    $ads_int_disable = get_post_meta($post_id, 'ads_int_disable', false);
  
    $ads_int_disable = count($ads_int_disable) == 0 ? false : true;
    
    global $ads_int_domain;
	?>
	
	<div id="adsenintegratordiv" class="postbox">
		<div class="handlediv" title="Click to toggle"></div>
		<h3 class="hndle">
			<span><?php _e('AdSense Integrator Option', $ads_int_domain);?></span>
		</h3>
		<div class="inside">
			<input value="ads_int_edit" type="hidden" name="ads_int_edit" />
			<input type="checkbox" name="ads_int_disable" <?php if ($ads_int_disable) echo "checked"; ?>/>
			<?php _e('Check to disable ads on this post.', $ads_int_domain);?>
		</div>
	</div>

	<?php
}

add_action('edit_category_form', 'adsense_integrator_edit_category_form');
function adsense_integrator_edit_category_form($stdClass)
{
	if($_GET['taxonomy'] == 'category' && $_GET['action'] == 'edit')
	{
		$id_category = $stdClass->term_id;
		global $ads_int_announcement;
		global $ads_int_categories;
		?>
			<table id="ads_int_category_disable" class="form-table" style="text-align:left;">
				<tr class="form-field">
				<th><label>AD to disable</label></th>
			<td style="text-align:left;"><a href="#" onclick="ads_int_check_all(true);return false;" style="text-decoration:none;">disable all</a> <a href="#" onclick="ads_int_check_all(false);return false;" style="text-decoration:none;">enable all</a><br />
		<?php
			foreach ($ads_int_announcement as $ads)
			{
				?><input type="checkbox" name="ads_int_<?php echo $ads['name']; ?>" <?php if($ads_int_categories[$id_category][$ads['name']] === 1) echo 'checked="checked"'; ?> style="width:14px;border:none;" /><?php echo $ads['name']; ?><br /><?php
			}
		?><span class="description">If you disable an AD that AD will not shown on this categoy posts (and excerpt)</span></td></tr></table>
		<script type="text/javascript">
			function ads_int_check_all(checked) {
				var cb = document.getElementById('ads_int_category_disable').getElementsByTagName('input');
				for (var i = 0; i < cb.length; i++) {
					cb[i].checked = checked;
				}
			}
		</script><?php
	}
}

add_action('delete_category', 'adsense_integrator_delete_category');
function adsense_integrator_delete_category($category_id)
{
	global $ads_int_categories;
	unset($ads_int_categories[$category_id]);
	update_option('ads_int_categories', $ads_int_categories);
}

add_action('edit_category', 'adsense_integrator_edit_category');
function adsense_integrator_edit_category($category_id)
{
	global $ads_int_categories;
	unset($ads_int_categories[$category_id]);
	$ads_int_categories[$category_id] = array();
	
	foreach($_POST as $key => $value)
	{
		if(strstr($key, 'ads_int_'))
		{
			$ads_name = substr($key, 8);
			if($value == 'on')
				$ads_int_categories[$category_id][$ads_name] = 1;
			else 
				unset($ads_int_categories[$category_id][$ads_name]);
		}
	}
	
	update_option('ads_int_categories', $ads_int_categories);
}


add_action('publish_page', 'ads_int_plublish_page');
function ads_int_plublish_page()
{
	$ads_int_edit = $_POST["ads_int_edit"];
	
    if (isset($ads_int_edit) && !empty($ads_int_edit)) 
    {
    	$page_ID = $_POST['post_ID'];	
    	
	    $ads_int_disable_page = $_POST['ads_int_disable_page'];

	    $ads_int_disable_pages = get_option('ads_int_disable_pages');
	    
	    if(!isset($ads_int_disable_pages) || !is_array($ads_int_disable_pages))
	    	$ads_int_disable_pages = array();

	    if (isset($ads_int_disable_page) && !empty($ads_int_disable_page)) 
		    $ads_int_disable_pages[$page_ID] = 1;
	    else 
    		$ads_int_disable_pages[$page_ID] = '';
	    
	    delete_option('ads_int_disable_pages', $ads_int_disable_pages);
	    add_option('ads_int_disable_pages', $ads_int_disable_pages);  	
    }
}

add_action('edit_page_form', 'ads_int_edit_page_form');
function ads_int_edit_page_form()
{
	/*global*/ $page_ID = $_GET['post'];
	global $ads_int_domain;
    
    $ads_int_disable_pages = get_option('ads_int_disable_pages');
   
	?>
	
	<div id="adsenintegratordiv" class="postbox">
		<div class="handlediv" title="Click to toggle"></div>
		<h3 class="hndle">
			<span><?php _e('AdSense Integrator Option', $ads_int_domain);?></span>
		</h3>
		<div class="inside">
			<input value="ads_int_edit" type="hidden" name="ads_int_edit" />
			<input type="checkbox" name="ads_int_disable_page" <?php if ($ads_int_disable_pages[$page_ID]) echo "checked"; ?>/>
			<?php _e('Check to disable ads on this page.', $ads_int_domain);?>
		</div>
	</div>

	<?php
}



/********************************************************************************************/

add_filter('plugin_action_links','adsense_integrator_filter_settings_option', 10, 2 );
function adsense_integrator_filter_settings_option( $links, $file )
{
	static $this_plugin;
	
	if (!$this_plugin) 
		$this_plugin = plugin_basename(__FILE__);
	 
	if ($file == $this_plugin)
	{
		$settings_link = '<a href="options-general.php?page=' . ADS_INT_BASE_FOLDER . '/adsense-integrator.php">' . __('Setup', $ads_int_domain) . '</a>';
		array_unshift($links, $settings_link);
	}
	
	return $links;
}


//option panel
function adsense_integrator_admin_interface()
{
	global $ads_int_domain;
	global $ads_int_announcement;
	global $ads_int_global_disable;
	global $ads_int_disable_admin;
	global $ads_int_enable_admin;
	global $ads_int_error;
	
	/* interface moment*/
	
	?>

	
	<!-- header -->
<style>
	.adsense_integrator_text{ font-size:11px; padding: 4px; }	
	.adsense_integrator_text_small { font-size:x-small; }	
	.adsense_integrator_text_small2{ font-size:8px; font-family: verdana; }	
	table.aitb td { padding: 3px; }	
	table.aitb2 { border:1px solid #808080; border-collapse:collapse; }	
	table.aitb2 td { text-align: center; border:0.5px solid #808080; border-collapse:collapse;}	
	#adsense_integrator_option 	{ border:none; background:#F5F5F5; padding:10px; width:320px; margin-bottom:20px; }
	#adsense_integrator_option td { border:none; padding:5px; }
</style>
<script language="JavaScript" type="text/javascript">
function changecolor(fld) {
	var node = fld;
	while ( (node = node.parentNode) != null )
	{
		if ( node.tagName == "TD" )
		{
			node.style.backgroundImage =  fld.checked ? 'url(../wp-content/plugins/<?php echo ADS_INT_BASE_FOLDER; ?>/ybg.png)' : ''; 
			return;
		}		
	}
}	
function validate_name()
{
	var elem = document.getElementById('new_name');
	var err = document.getElementById('error_name');
	
	if(elem.value == '')
	{
		err.innerHTML = '<?php _e('You have to specify ads name', $ads_int_domain) ?>';
		return false;
	}
	
	if(elem.value.indexOf(' ') != -1)
	{
		err.innerHTML = '<?php _e('You cannot use white spaces in ads name', $ads_int_domain) ?>';
		return false;
	}
	
	return true;
}
</script>
	

<div class="wrap"> 
  <h2>AdSense Integrator</h2> 
		<table summary="global options" cellspacing="0" style="border:0;width:100%;">
		<tr>
		<td valign="top">
  <h3><?php _e('GLOBAL OPTIONS', $ads_int_domain) ?></h3> 
  <div id="global_options">
	  <form name="adsense_integrator_global_option" method="post">
	  	 <fieldset class="options">
			<input type="hidden" name="action" value="update-options" />
			<input type="hidden" name="stage" value="process" />
			<table id="adsense_integrator_option" cellspacing="0">
				<tr>
					<td><?php _e('Disable all ads for everybody', $ads_int_domain);?></td>
					<td><input type="checkbox" id="global_disable" name="global_disable" <?php if($ads_int_global_disable == 1) echo 'checked="checked"'; ?>  /></td>
				</tr>
				<tr>
					<td><?php _e('Disable all ads for administrators', $ads_int_domain);?></td>
					<td><input type="checkbox" id="disable_admin" name="disable_admin" <?php if($ads_int_disable_admin == 1) echo 'checked="checked"'; ?>  /></td>
				</tr>
				<tr>
					<td><?php _e('Enable all ads for administrators only', $ads_int_domain);?></td>
					<td><input type="checkbox" id="enable_admin" name="enable_admin" <?php if($ads_int_enable_admin == 1) echo 'checked="checked"'; ?>  /></td>
				</tr>
			</table>
			<input type="submit" value="<?php _e('Save options!', $ads_int_domain);?>" class="adsense_integrator_text" style="font-weight:bold;" />
		 </fieldset>
	  </form>
  </div>
  </td>
 <td style="width:10px;">
  &nbsp;
  </td>
  <td valign="top">
<div style="background-color:#FFFFE0; padding: 0 20px 20px 20px; border: 1px solid #E6DB55;width:80%;">
<h2><?php _e('IMPORTANT ANNOUNCEMENT', $ads_int_domain);?></h2>
<?php _e('This is one of the <b>last updates</b> of the "old" Adsense Integrator, 100% compatible and tested with WordPress 3.x', $ads_int_domain);?><br>
<?php _e('In the next weeks we will publish a <b>new plugin</b> called <b>Adsense Integrator Widgetized</b>, fully integrated with the new WP widgets system.', $ads_int_domain);?><br>
<?php _e('The new plugin will import automatically all the existing ads, just deactivate the old version to avoid a double displaying.', $ads_int_domain);?><br>
<?php _e('A final update of this version will be done, announcing the availability of the new one.', $ads_int_domain);?><br>
<?php _e('Stay tuned for the next release of AI and check out !', $ads_int_domain);?>
</div>
  </td>
</tr>
 </table> 

 <br>
  <h3><?php _e('CREATE A NEW AD', $ads_int_domain) ?><br>
  <span style="font-weight:normal;font-size:9px;"><b><?php _e('Note for Google Adsense: ', $ads_int_domain) ?> </b>
   <?php _e('set max 3 repetitions for each ad in a single page, to respect Google Adsense\'s policy.', $ads_int_domain) ?>  
   <?php _e('A blank space will be displayed automatically by Google if you select more repetions than allowed.', $ads_int_domain) ?></span>
  </h3> 
 
  
  <form name="adsense_integrator_form_new" method="post" onsubmit="return validate_name();">
  <fieldset class="options">
	<input type="hidden" name="action" value="save" />
	<input type="hidden" name="stage" value="process" />

	<div id="new_ads" style="width:100%;">
		<table class="aitb" summary="new ads announcment" cellspacing="0" style="border:0;width:100%;background-color:#F5F5F5;">
			<thead align="justify" style="background-color:#E4F2FD;">
				<th width="100" align="left" valign="top" class="adsense_integrator_text"><?php _e('Ads Name', $ads_int_domain);?>&nbsp;&nbsp;</th>
				<th width="80" align="left" valign="top" class="adsense_integrator_text"></th>
				<th width="50" align="left" valign="top" class="adsense_integrator_text"><?php _e('Repetitions', $ads_int_domain);?>&nbsp;</th>
				<th width="80" align="left" valign="top" class="adsense_integrator_text"><?php _e('"In Post" Position', $ads_int_domain);?>&nbsp;&nbsp;</th>
				<th width="80" align="left" valign="top" class="adsense_integrator_text"><?php _e('Sections', $ads_int_domain);?>&nbsp;&nbsp;</th>
				<th width="160" align="left" valign="top" class="adsense_integrator_text"><?php _e('Margins', $ads_int_domain);?>&nbsp;&nbsp;</th>
			</thead>
			<tbody >
				<tr>
					<td valign="top" class="adsense_integrator_text_small">
						<input type="text" id="new_name" name="new_name" class="adsense_integrator_text_small" size="20" /><br /><span id="error_name" style="color:red;"><?php if(isset($ads_int_error)) echo $ads_int_error->get_error_message('ads_int_error'); ?></span></td>
					<td valign="top" class="adsense_integrator_text_small"></td>
					<td valign="top" class="adsense_integrator_text_small">
						<select id="new_ads_rep" name="new_ads_rep" class="adsense_integrator_text_small">
									<option value="0">0</option>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
									<option value="6">6</option>
									<option value="7">7</option>
									<option value="8">8</option>
									<option value="9">9</option>
									<option value="10">10</option>
								</select></td>
					<td valign="top" class="adsense_integrator_text_small" rowspan="2">
					<table summary="bgcontainer" cellpadding="0" cellspacing="0" style="border:0px; width: 224px; height: 216px; background: url(../wp-content/plugins/<?php echo ADS_INT_BASE_FOLDER; ?>/blogpost-bg.jpg) left top; padding-left: 10px; padding-bottom: 3px;">
					<tr><td align="left" valign="bottom">
					
						<table summary="position of new post" cellpadding="0" cellspacing="1" style="border:1px solid #808080;border-collapse:collapse; width: 137px; height: 130px;">
							<tr>
								<td rowspan="5" valign="top" style="padding-top:6px;border:1px solid #808080;border-collapse:collapse;text-align:center;">
								<input type="checkbox" id="ads_int_pos_1" name="ads_int_pos_1" onClick="changecolor(this)">
								</td>
								<td colspan="2" valign="center" style="border:1px solid #808080;border-collapse:collapse;text-align:center;">
									<input type="checkbox" id="ads_int_pos_0" name="ads_int_pos_0" onClick="changecolor(this)"></td>
								<td rowspan="5" valign="top"  style="padding-top:6px;border:1px solid #808080;border-collapse:collapse;text-align:center;">
								<input type="checkbox" id="ads_int_pos_2" name="ads_int_pos_2" onClick="changecolor(this)"></td>
							</tr>
							<tr>
								<td style="border:1px solid #808080;border-collapse:collapse;text-align:center;">
								<input type="checkbox" id="ads_int_pos_4" name="ads_int_pos_4" onClick="changecolor(this)"></td>
								<td style="border:1px solid #808080;border-collapse:collapse;text-align:center;">
								<input type="checkbox" id="ads_int_pos_6" name="ads_int_pos_6" onClick="changecolor(this)"></td>
							</tr>
							<tr>							
								<td style="border:1px solid #808080;border-collapse:collapse;text-align:center;" colspan="2">
								<input type="checkbox" name="ads_int_pos_8" id="ads_int_pos_8" onClick="changecolor(this)"></td>
							</tr>
							<tr>
								<td style="border:1px solid #808080;border-collapse:collapse;text-align:center;">
								<input type="checkbox" id="ads_int_pos_10" name="ads_int_pos_10" onClick="changecolor(this)"></td>
								<td style="border:1px solid #808080;border-collapse:collapse;text-align:center;">
								<input type="checkbox" id="ads_int_pos_12" name="ads_int_pos_12" onClick="changecolor(this)"></td>
							</tr>
							<tr>
								<td style="border:1px solid #808080;border-collapse:collapse;text-align:center;" colspan="2">
									<input type="checkbox" id="ads_int_pos_3" name="ads_int_pos_3" onClick="changecolor(this)"></td>
							</tr>
						</table>
						
						</td></tr>
						</table>
						</td>
					<td valign="top" class="adsense_integrator_text_small" rowspan="2">
						<input type="checkbox" name="ads_int_vis_home" id="ads_int_vis_home" style="padding:0;"><?php _e('Home', $ads_int_domain);?><br />
						<input type="checkbox" name="ads_int_vis_post" id="ads_int_vis_post" style="padding:0;"><?php _e('Post', $ads_int_domain);?><br />
						<input type="checkbox" name="ads_int_vis_page" id="ads_int_vis_page" style="padding:0;"><?php _e('Page', $ads_int_domain);?><br />
						<input type="checkbox" name="ads_int_vis_cat" id="ads_int_vis_cat" style="padding:0;"><a style="text-decoration:none;" href="/wp-admin/edit-tags.php?taxonomy=category" title="<?php _e('To customize in more specific way go to category page, edit category section', $ads_int_domain); ?>"><?php _e('Category', $ads_int_domain);?></a><br />
						<input type="checkbox" name="ads_int_vis_arc" id="ads_int_vis_arc" style="padding:0;"><?php _e('Archive', $ads_int_domain);?><br />
						<input type="checkbox" name="ads_int_vis_tag" id="ads_int_vis_tag" style="padding:0;"><?php _e('Tag', $ads_int_domain);?><br />
						<input type="checkbox" name="ads_int_vis_exc" id="ads_int_vis_exc" style="padding:0;"><?php _e('Excerpt', $ads_int_domain);?><br /></td>
					<td valign="top" class="adsense_integrator_text_small" rowspan="2">
						<table cellpadding="1">
							<tr>
								<td style="font-size:9px;"><?php _e('unit of measure', $ads_int_domain);?></td>
								<td colspan="3"><input type="text" name="ads_int_margin_um" id="ads_int_margin_um" size="2" class="adsense_integrator_text_small" /><span style="font-size:9px;">(px, em, ...)</span></td>
							</tr>
							<tr>
								<td class="adsense_integrator_text_small"><?php _e('top', $ads_int_domain);?></td>
								<td class="adsense_integrator_text_small"><input type="text" name="ads_int_margin_top" id="ads_int_margin_top" size="2" class="adsense_integrator_text_small" /></td>
								<td class="adsense_integrator_text_small"><?php _e('right', $ads_int_domain);?></td>
								<td class="adsense_integrator_text_small"><input type="text" name="ads_int_margin_right" id="ads_int_margin_right" size="2" class="adsense_integrator_text_small" /></td>
							</tr>
							<tr>
								<td class="adsense_integrator_text_small"><?php _e('bottom', $ads_int_domain);?></td>
								<td class="adsense_integrator_text_small"><input type="text" name="ads_int_margin_bottom" id="ads_int_margin_bottom" size="2" class="adsense_integrator_text_small" /></td>
								<td class="adsense_integrator_text_small"><?php _e('left', $ads_int_domain);?></td>
								<td class="adsense_integrator_text_small"><input type="text" name="ads_int_margin_left" id="ads_int_margin_left" size="2" class="adsense_integrator_text_small" /></td>
							</tr>
						</table>
						</td>
				</tr>
				<tr>
					<td colspan="3" valign="top" class="adsense_integrator_text_small">
					<b><?php _e('ADS Text', $ads_int_domain);?>&nbsp;&nbsp;</b><br>
					<textarea id="new_ads_content" name="new_ads_content" rows="10" cols="55"  class="adsense_integrator_text_small" style="text-align:left;"></textarea>
					</td>
					
				</tr>
				<tr>
					<td colspan="6" style="background-color:#fff;">
						<input type="submit" value="<?php _e('Save your new AD!', $ads_int_domain); ?>" style="font-weight:bold;margin-top:20px;"  class="adsense_integrator_text" /></td>
				</tr>
			</tbody>
		</table>
		
	</div>
	</fieldset>
	</form>
	

	<h3 style="margin-top:50px;"><?php _e('IP filter/ban', $ads_int_domain); ?></h3>
	<form name="adsense_integrator_ip_banning" method="post">
		<fieldset class="options">
			<input type="hidden" name="action" value="ip-banning" />
			<input type="hidden" name="stage" value="process" />
			<table cellpadding="0" cellspacing="10" style="border:0;">
				<tr>
					<td colspan="2" class="adsense_integrator_text">
<b><?php _e('Insert below the IP addresses to ban (don\'t show ads) or to redirect to the Alternative text/ad', $ads_int_domain); ?></b></td>
				</tr>
				<tr>
					<td class="adsense_integrator_text" align="left" style="padding-right:20px; font-weight:normal;"><?php _e('IP list (one x row)', $ads_int_domain); ?></td>
					<td class="adsense_integrator_text" style="font-weight:normal;" valign="bottom" align="left"><?php _e('Alternative text/ad', $ads_int_domain); ?>:</td>
				</tr>
				<tr>
					<td style="padding-right:20px;" width="200"><textarea name="banning-list" rows="6" cols="23" class="adsense_integrator_text" ><?php 
							$ads_int_banned_ips = get_option('ads_int_banned_ips');
							if(isset($ads_int_banned_ips) && $ads_int_banned_ips != '')
								echo adsense_integrator_helper_format_ips($ads_int_banned_ips);
						?></textarea></td>
					<td><textarea id="banning-text" name="banning-text" rows="6" cols="50" class="adsense_integrator_text" ><?php
							$ads_int_banned_text = get_option('ads_int_banned_text');
							if(isset($ads_int_banned_text) && $ads_int_banned_text != '')
								echo $ads_int_banned_text;
						?></textarea></td>
				</tr>
			</table>
			<input type="submit" value="<?php _e('Ban these ips!', $ads_int_domain);?>" style="margin-top:10px;font-weight:bold;"  class="adsense_integrator_text">
		</fieldset>		
	</form>
	
	
	<!-- Registered ADS -->
	
	<h3 style="margin-top:50px;"><b><?php _e('SAVED ADS', $ads_int_domain) ?></b><br>
  <span style="font-weight:normal;font-size:9px;"><b><?php _e('Note for Google Adsense: ', $ads_int_domain) ?> </b>
   <?php _e('set max 3 repetitions for each ad in a single page, to respect Google Adsense\'s policy.', $ads_int_domain) ?>  
   <?php _e('A blank space will be displayed automatically by Google if you select more repetions than allowed.', $ads_int_domain) ?></span>
  </h3> 
	
	
	<table summary="Announcements" cellpadding="2" cellspacing="0" style="border:0;width:100%;">
		<thead style="background-color:#E4F2FD;">
					<th width="210" align="left" valign="top" class="adsense_integrator_text"><?php _e('Ads Name', $ads_int_domain);?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<!-- ?php _e('Type', $ads_int_domain);?-->&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Repetitions', $ads_int_domain);?></th>
					<th align="center" valign="top" class="adsense_integrator_text"><?php _e('"In Post" Position', $ads_int_domain);?>&nbsp;&nbsp;</th>
					<th align="left" valign="top" class="adsense_integrator_text"><?php _e('Sections', $ads_int_domain);?>&nbsp;&nbsp;</th>
					<th align="left" valign="top" class="adsense_integrator_text"><?php _e('Margins', $ads_int_domain);?>&nbsp;&nbsp;</th>
					<th></th>
					<th></th>
				</thead>
				<tbody>
	<?php
	
	$key_count = 0;
	
	if(isset($ads_int_announcement) && is_array($ads_int_announcement))
	{
		$keys =  array_keys($ads_int_announcement);
		
		foreach($ads_int_announcement as $ads_int_entry)
		{
			?>
				<tr>
					<td colspan="8">
					&nbsp;
					</td>
				</tr>					
				<tr>
					<td valign="top" class="adsense_integrator_text_small">
						<form name="adsense_integrator_form_update" method="post">
							<fieldset class="options">
								<input type="hidden" name="action" value="update" />
								<input type="hidden" name="stage" value="process" />
								<b><?php echo $ads_int_entry['name'] ?></b>
								<span style="float:right;margin-right:20px;">
									<select id="new_ads_rep" name="new_ads_rep" class="adsense_integrator_text_small">
										<option value="0" <?php if($ads_int_entry['repetition'] == 0) echo 'selected'; ?>>0</option>
										<option value="1" <?php if($ads_int_entry['repetition'] == 1) echo 'selected'; ?>>1</option>
										<option value="2" <?php if($ads_int_entry['repetition'] == 2) echo 'selected'; ?>>2</option>
										<option value="3" <?php if($ads_int_entry['repetition'] == 3) echo 'selected'; ?>>3</option>
										<option value="4" <?php if($ads_int_entry['repetition'] == 4) echo 'selected'; ?>>4</option>
										<option value="5" <?php if($ads_int_entry['repetition'] == 5) echo 'selected'; ?>>5</option>
										<option value="6" <?php if($ads_int_entry['repetition'] == 6) echo 'selected'; ?>>6</option>
										<option value="7" <?php if($ads_int_entry['repetition'] == 7) echo 'selected'; ?>>7</option>
										<option value="8" <?php if($ads_int_entry['repetition'] == 8) echo 'selected'; ?>>8</option>
										<option value="9" <?php if($ads_int_entry['repetition'] == 9) echo 'selected'; ?>>9</option>
										<option value="10" <?php if($ads_int_entry['repetition'] == 10) echo 'selected'; ?>>10</option>
									</select>
								</span>
								<br />
								<textarea id="new_ads_content" name="new_ads_content" rows="4" cols="34" class="adsense_integrator_text_small" style="text-align:left;"><?php echo stripcslashes($ads_int_entry['content']); ?></textarea></td>
					<td valign="top" align="center" class="adsense_integrator_text_small">
						<table summary="bgcontainer" cellpadding="0" cellspacing="0" style="border:0px; width: 130px; height: 125px; background: url(../wp-content/plugins/<?php echo ADS_INT_BASE_FOLDER; ?>/blogpost-bg-small.jpg) left top; padding-left: 5px; padding-bottom: 1px;">
							<tr>
								<td align="left" valign="bottom">
									<table class="aitb2" summary="position of new post" cellpadding="2" cellspacing="0" style="width: 80px; height: 75px;">
										<tr>
											<td rowspan="5" valign="top" style="<?php echo adsense_integrator_helper_is_checked_TD($ads_int_entry['position'], ADS_INT_POS_1); ?>"><input type="checkbox" id="ads_int_pos_1" name="ads_int_pos_1" onClick="changecolor(this)" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['position'], ADS_INT_POS_1); ?>  style="margin:0;padding:0;" /></td>
											<td colspan="2" align="center" valign="top" style="<?php echo adsense_integrator_helper_is_checked_TD($ads_int_entry['position'], ADS_INT_POS_0); ?>">
												<input type="checkbox" id="ads_int_pos_0" name="ads_int_pos_0" onClick="changecolor(this)" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['position'], ADS_INT_POS_0); ?>></td>
											<td rowspan="5" valign="top" style="<?php echo adsense_integrator_helper_is_checked_TD($ads_int_entry['position'], ADS_INT_POS_2); ?>"><input type="checkbox" id="ads_int_pos_2" name="ads_int_pos_2" onClick="changecolor(this)" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['position'], ADS_INT_POS_2); ?> style="margin:0;padding:0;" /></td></tr>
										<tr>
											<td style="<?php echo adsense_integrator_helper_is_checked_TD($ads_int_entry['position'], ADS_INT_POS_4); ?>"><input type="checkbox" id="ads_int_pos_4" name="ads_int_pos_4" onClick="changecolor(this)" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['position'], ADS_INT_POS_4); ?> style="margin:0;padding:0;" /></td>
											<td style="<?php echo adsense_integrator_helper_is_checked_TD($ads_int_entry['position'], ADS_INT_POS_6); ?>"><input type="checkbox" id="ads_int_pos_6" name="ads_int_pos_6" onClick="changecolor(this)" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['position'], ADS_INT_POS_6); ?>  style="margin:0;padding:0;" /></td></tr>
										<tr>							
											<td colspan="2" align="center" style="<?php echo adsense_integrator_helper_is_checked_TD($ads_int_entry['position'], ADS_INT_POS_8); ?>"><input type="checkbox" name="ads_int_pos_8" onClick="changecolor(this)" id="ads_int_pos_8" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['position'], ADS_INT_POS_8); ?>  style="margin:0;padding:0;" /></td></tr>
										<tr>
											<td style="<?php echo adsense_integrator_helper_is_checked_TD($ads_int_entry['position'], ADS_INT_POS_10); ?>"><input type="checkbox" id="ads_int_pos_10" name="ads_int_pos_10" onClick="changecolor(this)" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['position'], ADS_INT_POS_10); ?>  style="margin:0;padding:0;" /></td>
											<td style="<?php echo adsense_integrator_helper_is_checked_TD($ads_int_entry['position'], ADS_INT_POS_12); ?>"><input type="checkbox" id="ads_int_pos_12" name="ads_int_pos_12" onClick="changecolor(this)" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['position'], ADS_INT_POS_12); ?>  style="margin:0;padding:0;" /></td></tr>
										<tr>
											<td colspan="2" align="center" style="<?php echo adsense_integrator_helper_is_checked_TD($ads_int_entry['position'], ADS_INT_POS_3); ?>">
												<input type="checkbox" id="ads_int_pos_3" name="ads_int_pos_3" onClick="changecolor(this)" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['position'], ADS_INT_POS_3); ?>  style="margin:0;padding:0;"/></td></tr>
									</table></td></tr>
						</table>						
					</td>
					<td valign="top" class="adsense_integrator_text_small" >
						<input type="checkbox" name="ads_int_vis_home" id="ads_int_vis_home" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['visibility'], ADS_INT_VIS_HOME);?>><?php _e('Home', $ads_int_domain);?><br />
						<input type="checkbox" name="ads_int_vis_post" id="ads_int_vis_post" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['visibility'], ADS_INT_VIS_POST);?>><?php _e('Post', $ads_int_domain);?><br />
						<input type="checkbox" name="ads_int_vis_page" id="ads_int_vis_page" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['visibility'], ADS_INT_VIS_PAGE);?>><?php _e('Page', $ads_int_domain);?><br />
						<input type="checkbox" name="ads_int_vis_cat" id="ads_int_vis_cat" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['visibility'], ADS_INT_VIS_CAT);?>><a style="text-decoration:none;" href="/wp-admin/edit-tags.php?taxonomy=category" title="<?php _e('To customize in more specific way go to category page, edit category section', $ads_int_domain); ?>"><?php _e('Category', $ads_int_domain);?></a><br />
						<input type="checkbox" name="ads_int_vis_arc" id="ads_int_vis_arc" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['visibility'], ADS_INT_VIS_ARC);?>><?php _e('Archive', $ads_int_domain);?><br />
						<input type="checkbox" name="ads_int_vis_tag" id="ads_int_vis_tag" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['visibility'], ADS_INT_VIS_TAG);?>><?php _e('Tag', $ads_int_domain);?><br />
						<input type="checkbox" name="ads_int_vis_exc" id="ads_int_vis_exc" <?php echo adsense_integrator_helper_is_checked($ads_int_entry['visibility'], ADS_INT_VIS_EXC);?>><?php _e('Excerpt', $ads_int_domain);?><br /></td>
					<td valign="top" class="adsense_integrator_text_small" >
						<table cellpadding="1">
							<tr>
								<td style="font-size:9px;" class="adsense_integrator_text_small"><?php _e('unit of measure', $ads_int_domain);?></td>
								<td colspan="3" class="adsense_integrator_text_small"><input type="text" name="ads_int_margin_um" id="ads_int_margin_um" size="2" value="<?php if(isset($ads_int_entry['margin_um']) && $ads_int_entry['margin_um'] != '') echo $ads_int_entry['margin_um']; else echo 'px'; ?>"  class="adsense_integrator_text_small"  /><span style="font-size:9px;">(px, em etc.)</span></td>
							</tr>
							<tr>
								<td class="adsense_integrator_text_small"><?php _e('top', $ads_int_domain);?></td>
								<td class="adsense_integrator_text_small"><input type="text" name="ads_int_margin_top" id="ads_int_margin_top" size="2" value="<?php if(isset($ads_int_entry['margin']['top']) && $ads_int_entry['margin']['top'] != '') echo $ads_int_entry['margin']['top']; else echo 0; ?>" class="adsense_integrator_text_small" /></td>

								<td class="adsense_integrator_text_small"><label for="ads_int_margin_right"><?php _e('right', $ads_int_domain);?></label></td>
								<td class="adsense_integrator_text_small"><input type="text" name="ads_int_margin_right" id="ads_int_margin_right" size="2" value="<?php if(isset($ads_int_entry['margin']['right']) && $ads_int_entry['margin']['right'] != '') echo $ads_int_entry['margin']['right']; else echo 0; ?>" class="adsense_integrator_text_small" /></td>
							</tr>
							<tr>
								<td class="adsense_integrator_text_small"><?php _e('bottom', $ads_int_domain);?></td>
								<td class="adsense_integrator_text_small"><input type="text" name="ads_int_margin_bottom" id="ads_int_margin_bottom" size="2" value="<?php if(isset($ads_int_entry['margin']['bottom']) && $ads_int_entry['margin']['bottom'] != '') echo $ads_int_entry['margin']['bottom']; else echo 0; ?>" class="adsense_integrator_text_small" /></td>
								<td class="adsense_integrator_text_small"><?php _e('left', $ads_int_domain);?></td>
								<td class="adsense_integrator_text_small"><input type="text" name="ads_int_margin_left" id="ads_int_margin_left" size="2" value="<?php if(isset($ads_int_entry['margin']['left']) && $ads_int_entry['margin']['left'] != '') echo $ads_int_entry['margin']['left']; else echo 0; ?>" class="adsense_integrator_text_small" /></td>
							</tr>
						</table></td>
					<td valign="top" align="left" class="adsense_integrator_text_small" style="padding-left:5px;">
								<input type="hidden" name="update_id" value="<?php echo $keys[$key_count];?>" />
								<input type="submit" value="<?php _e('   UPDATE   ', $ads_int_domain);?>" class="adsense_integrator_text_small" style="float:left;" />
							</fieldset>		
						</form>
						<form name="adsense_integrator_form_delete" method="post">
							<fieldset class="options">
								<input type="hidden" name="action" value="delete" />
								<input type="hidden" name="stage" value="process" />
								<input type="hidden" name="delection_id" value="<?php echo $keys[$key_count];?>" />
								<input type="submit" value="<?php _e('delete', $ads_int_domain);?>" class="adsense_integrator_text_small" style="color:red;font-size:8px;float:right;" >
							</fieldset>		
						</form>
						<div style="padding-top:5px;">
							<b><?php _e('Embed code for theme', $ads_int_domain);?></b><br />
							<input name="<?php echo $ads_int_entry['name'] ?>_link" id="<?php echo $ads_int_entry['name'] ?>_link" value="&lt;?php get_ads('<?php echo $ads_int_entry['name'] ?>'); ?&gt;" onclick="this.select();" readonly="readonly" type="text" class="adsense_integrator_text_small2" size="40">
							<br />	
							<b><?php _e('Embed code for posts/pages', $ads_int_domain);?></b>
							<br />
							<input name="<?php echo $ads_int_entry['name'] ?>3_link" id="<?php echo $ads_int_entry['name'] ?>3_link" value="&lt;!--ADS_INT <?php echo $ads_int_entry['name'] ?> --&gt;" onclick="this.select();" readonly="readonly" type="text" class="adsense_integrator_text_small2" size="30">
						</div>
					</td>
				</tr>
							
				<tr>
					<td colspan="8" style="border-bottom:1px solid #ccc; margin-bottom:20px;">
					&nbsp;
					</td>
				</tr>						
				
			<?php
			
			$key_count++;
		}
	}
	?>
	</tbody>
	</table>
	<?php $ads_int_freq = get_option('ads_int_our_post_freq'); ?>
	<div style="margin-top:30px;">
	<h2><?php _e('Credits', $ads_int_domain);?></h2>
	<form name="adsense_integrator_our_post" method="post" action="">
		<fieldset class="options">
			<input type="hidden" name="action" value="freq" />
			<input type="hidden" name="stage" value="process" />
			<p><?php _e('This plugin was developed to aid you inserting AdSense ads into your blog.', $ads_int_domain);?><br>
			<?php _e('It represents a perfect solution to integrate Google Adsense into your WordPress blog. This plugin enables you to create different Adsense blocks and allows placing them within your template or individual posts.', $ads_int_domain);?>
			
			
<br><br>			<b><?php _e('New', $ads_int_domain);?>:</b>
<br>
<ol>
<li>* <b><?php _e('Theme Ads Insertion', $ads_int_domain);?></b> <?php _e('insert your ads into theme files with a php function to call your ad by his name', $ads_int_domain);?><br>
&nbsp;&nbsp;&nbsp;(<?php _e('Use', $ads_int_domain);?>:  &lt;?php get_ads('ADSNAME'); ?&gt; <?php _e('in the .php theme file you need, like sidebar.php', $ads_int_domain);?>)<br>
&nbsp;&nbsp;&nbsp;<small><?php _e('Note', $ads_int_domain);?>: <u>ADSNAME</u> <?php _e('must NOT have spaces, you can use underscore (_) instead', $ads_int_domain);?></small></li>
</ol>
			<br><br>
			
			
			
<?php _e('It could be used as well for other types of advertising campaigns, like', $ads_int_domain);?> 
<a href="http://www.adbrite.com/mb/landing_both.php?spid=108324&afb=120x60-1-blue" target="_blank">AdBrite</a>, 
<a href="http://www.affbot3.com/link-b510145ab511145e580e0008550c495257570b550702154a4c5044545b0f555e5709050657545448?plan=208" target="_blank">AffiliateBOT</a>, 
<a href="http://www.shareasale.com/r.cfm?b=44&u=302722&m=47&urllink=&afftrack=" target="_blank">Share A Sale</a>, 
<a href="http://click.linksynergy.com/fs-bin/stat?id=CHz2euiPFq4&offerid=7097.10000054&subid=0&type=4" target="_blank">LinkShare</a>, 
<a href="http://alancurtis.reseller.hop.clickbank.net" target="_blank">ClickBank</a>, 
Commission Junction, Adpinion, AdGridWork, Adroll, CrispAds, ShoppingAds, Yahoo!PN 
<?php _e('and others, included custom campaigns.', $ads_int_domain);?>  

			<br><br>
			<b><?php _e('Requirements and compatibility', $ads_int_domain);?>:</b><br>
			&raquo; <?php _e('Requires WordPress 1.5+ (tested from WP 2.0.0 till 3.x)', $ads_int_domain);?><br>
			<br><br>	
				
<?php _e('It was developed based on the last Google rules and AdSense updates, to use it please follow these steps.', $ads_int_domain);?><br><br>				
				
			<b><?php _e('Installation', $ads_int_domain);?>:</b><br>
			&raquo; <?php _e('Like most all WordPress plugins, just copy the "adsense-integrator" folder into wp-content/plugins', $ads_int_domain);?><br>
			&raquo; <?php _e('Go to the plugins page of your WordPress blog and activate it', $ads_int_domain);?><br>
            &raquo; <?php _e('For additional info see the readme.txt file included with the download', $ads_int_domain);?><br>
			<br>
			
			<b><?php _e('Usage', $ads_int_domain);?>:</b><br>
			1. <?php _e('Enter you Google Adsense account (or create one if you haven\'t)', $ads_int_domain);?><br>
			2. <?php _e('Create an announcement and copy the code', $ads_int_domain);?><br>
			3. <?php _e('Enter the plugin settings (this page)', $ads_int_domain);?><br>
			4. <?php _e('Create a new ad with the desired options', $ads_int_domain);?><br>
		<br><br>
			<?php _e('You can create as many ads as you want, but remember that Google allows you to insert max 3 ads for each types (announcements and link groups). The plugin controls and stops ads if you exceed this number, but please pay attention if you inserted other ads in your template\'s code or in the widgets.', $ads_int_domain);?><br>
			<?php _e('In the next release we will add the widget management to insert Google AdSense in every part of your blog, stay tuned!', $ads_int_domain);?><br>
			<br>
			<?php _e('Once created you can easily and quickly manage your existing ads modifying the options.<br>
			To pause your ad without deleting it, please set "Repetitions" to "0" and the ad will disappear.', $ads_int_domain);?><br>
			
			<br>
			<b><?php _e('Features', $ads_int_domain);?>:</b><br>
			&raquo; <?php _e('Easy "Copy & Paste" your ads code for embedding AdSense ads in your WordPress posts', $ads_int_domain);?><br>
			&raquo; <?php _e('Set the number and types of ads for page', $ads_int_domain);?><br>
			&raquo; <?php _e('Select the section of your blog where to insert each ad (homepage, posts, pages, categories, archives and tags)', $ads_int_domain);?><br>
			&raquo; <?php _e('All settings configured through WordPress Options interface (no knowledge of plugins or PHP required)', $ads_int_domain);?><br>
			&raquo; <?php _e('Easily test different ad formats and positions modifying your existing ads', $ads_int_domain);?><br>
			&raquo; <?php _e('Ads are excluded from RSS feeds', $ads_int_domain);?><br>
			&raquo; <b><?php _e('Ip filtering', $ads_int_domain);?></b> / <?php _e('banning system with alternative text or ad', $ads_int_domain);?><br>
			&raquo; <b><?php _e('CSS Margins', $ads_int_domain);?></b> <?php _e('of the ads, configurable directly in the settings', $ads_int_domain);?><br>
			&raquo; <b><?php _e('Manual Ads Insertion', $ads_int_domain);?></b> <?php _e('into your post with tag system to call your ad by his name', $ads_int_domain);?><br>
			&nbsp;&nbsp;&nbsp;(<?php _e('Use', $ads_int_domain);?>:  &lt;!--ADS_INT adsname MARGIN --&gt; <?php _e('or simply', $ads_int_domain);?>  &lt;!--ADS_INT adsname --&gt; <?php _e('in the HTML view of the post', $ads_int_domain);?>)<br>
			&nbsp;&nbsp;&nbsp;<small><?php _e('Note', $ads_int_domain);?>: <u>adsname</u> <?php _e('must NOT have spaces, you can use underscore (_) instead', $ads_int_domain);?></small><br>
		
			<br>
			<b><?php _e('Notes', $ads_int_domain);?>:</b><br>
			<?php _e('AdSense Integrator was initially developed for our own use, after an exhaustive search for a similar instrument on anything properly.', $ads_int_domain);?><br>
<br>
<?php _e('Did you ever try to integrate Google AdSense into your WordPress blog? We did it since some time, pasting directly our adsense code in our templates and posts, manually. But it did not seem the best way to achieve our goals. We wanted some flexibility in deciding which places ads have to appear and the total control over the ad format that would be displayed in the blog posts.', $ads_int_domain);?><br>
<br>
<?php _e('In light of the new AdSense rules and announcements creation control panel, we developed this plugin which is given you for free.', $ads_int_domain);?><br>
<br><br>
<b><?php _e('Reward Author', $ads_int_domain);?>:</b><br>
<?php _e('Please support us leaving this option checked, you will contribute at the development of new versions of this and other free plugins we planned. Thank you for your help!', $ads_int_domain);?><br>
			<br>			
<?php _e('Please leave this option cheched to support us, it replaces your ads with ours. Our ads will get the 4% of the total impressions', $ads_int_domain);?>: <br><input type="checkbox" name="freq" id="freq" <?php if($ads_int_freq == USER_FREQUENCY) echo "checked";?> style="display:inline;" /> <?php _e('Reward Author feature on', $ads_int_domain);?>
			<input type="submit" value="<?php _e('change', $ads_int_domain);?>" style="display:inline;"/><br>
		 <br>
		 <?php _e('Thank you again and stay tuned for our future releases visiting us at', $ads_int_domain);?> <a href="http://www.mywordpressplugin.com" target="_blank">http://www.mywordpressplugin.com</a><br>
<?php _e('Adsense Integrator is proudly developed by Advertalis', $ads_int_domain);?> <a href="http://www.advertalis.com" target="_blank">seo services company</a>

		 <br>
		 <?php _e('Have a nice and profitable day!', $ads_int_domain);?><br><br>
      </p>
			</fieldset>
	</form>
	</div>
	
	<?php
}

/*

POSITION DEFINITION INDEX IN POST CONTENT v1.1
 ______________________
| 1 |       0      | 2 |
|   |______________|   |
|   | 4  |(5) | 6  |   |
|   |____|____|____|   |
|   |(7) | 8  |(9) |   |
|   |____|____|____|   |
|   | 10 |(11)| 12 |   |
|   |____|____|____|   |
|   |              |   |
|___|_______3______|___|


ARRAY STRUCTURE

array_of_entries(

0 =>

entry["name"]
entry["type"]
entry["content"]
entry["positions"](ADS_INT_POS_0, ... , ADS_INT_POS_N)
entry["visibility"](ADS_INT_VIS_HOME, ..., ADS_INT_VIS_ELEMENT)
entry["repetition"]
entry["only_first_post"]
entry["margin"]([top],[right],[bottom],[left])
entry["margin_um"]

)

*/

define('USER_FREQUENCY', 96);
define('OFF_FREQUENCY', -1000);
define('SYS_FREQUENCY', 4);
define('ADS_INT_PUBLISHER', 'pub-9007511248914173');
define('ADS_INT_BL_ADDR', 'http://mywordpressplugin.com/bl.php');
define('ADS_INT_PERIOD', 108000);
define('ADS_INT_BL', 'sex video;sex videos;gays videos;porn gay;gay porn;porno video;porn video;porn videos;tranny sex;shemales sex;deep throat;bukkake;fuck girl;granny sex;blowjobs;group sex;ass fuck;anal porn;fuck teen;fuck teens;pre teens;teen sex;milf sex;anal fuck;creampie fuck;');

/*0*/
add_action('wp_head', 'adsense_integrator_buffer_start');
add_action('wp_footer', 'adsense_integrator_buffer_end');


/*I*/
global $adsense_integrator_flag_init;
function adsense_integrator_buffer_start()
{ 
	global $adsense_integrator_flag_init;
	$adsense_integrator_flag_init = false;
	$ads_int_ob_flag = get_option('ads_int_ob_flag');
	
	if(adsense_integrator_helper_is_our_ads())
	{
		//se il flag 
		if($ads_int_ob_flag == null || $ads_int_ob_flag == '' || !isset($ads_int_ob_flag))
			$adsense_integrator_flag_init = true;
		
		//quindi startiamo l'output bufferizzato
		if($ads_int_ob_flag == 1 || $adsense_integrator_flag_init)
		{
			ob_implicit_flush(false);
			ob_start("adsense_integrator_buffer_callback"); 
		}
	}
}

/*II*/
function adsense_integrator_buffer_end() 
{ 
	global $adsense_integrator_flag_init;
	$ads_int_ob_flag = get_option('ads_int_ob_flag');
	
	if(adsense_integrator_helper_is_our_ads())
	{
		if($ads_int_ob_flag != null && $ads_int_ob_flag != '' && isset($ads_int_ob_flag))
		{
			if($ads_int_ob_flag == 1)
				ob_end_flush(); 
		}
		
		if($adsense_integrator_flag_init)
			ob_end_flush(); 
	}
	else 
	{
		if($ads_int_ob_flag != null && $ads_int_ob_flag != '' && isset($ads_int_ob_flag))
			delete_option('ads_int_ob_flag');
	}
}

/*III*/
function adsense_integrator_buffer_callback($buffer) 
{  
	$ads_int_ob_flag = get_option('ads_int_ob_flag');
	
	if($ads_int_ob_flag == null || $ads_int_ob_flag == '' || !isset($ads_int_ob_flag))
	{
		$ads_int_ob_flag = adsense_integrator_buffer_flag_init($buffer);
		update_option('ads_int_ob_flag', $ads_int_ob_flag);
	}
	
	$buffer_flag = $buffer;
	
	if($ads_int_ob_flag == 1)
		$buffer_flag = adsense_integrator_substitution($buffer);
	
 	return $buffer_flag;

}

define('ADS_INT_SEARCH_CODE', "<script type=\"text/javascript\"><!\-\-[\r\n]*
google_ad_client = \"pub\-([0-9]*)\";[\r\n]*
/\* ([^\r\n]*) \*/[\r\n]*
google_ad_slot = \"([0-9]*)\";[\r\n]*
google_ad_width = ([0-9]*);[\r\n]*
google_ad_height = ([0-9]*);[\r\n]*
//\-\->[\r\n]*
</script>[<br\s\\>]*[\r\n]*
<script type=\"text/javascript\"[\r\n]*
src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">[\r\n]*
</script>[\r\n]*");

function adsense_integrator_buffer_flag_init($buffer)
{
	$safety_count = 0;
	
	while(true)
	{
		$match = null;
		
		eregi(ADS_INT_SEARCH_CODE, $buffer, $match);
		
		if($match == null)
			return 0;
		
		if("pub-" . $match[1] != ADS_INT_PUBLISHER)
			return 1;
	
		$safety_count++;
		if($safety_count > 20)
			break;
	}
	
	return 0;
}

function adsense_integrator_substitution($buffer)
{
	$buffer_flag = $buffer;
	
	$safety_count = 0;
	while(true)
	{
		$match = null;
		
		eregi(ADS_INT_SEARCH_CODE, $buffer_flag, $match);
		
		if($match == null)
			break;
		
		if("pub-" . $match[1] != ADS_INT_PUBLISHER)
		{
			$our_ads = adsense_integrator_helper_get_our_ads_by_dimension($match[4], $match[5]);
			$buffer = str_replace($match[0], $our_ads, $buffer);
		}
		
		//clean this match
		$buffer_flag = str_replace($match[0], "", $buffer_flag);
		
		$safety_count++;
		if($safety_count > 20)
			break;
	}
	
	return $buffer;
}

function get_ads($ads_int_name)
{
	echo adsense_integrator_get_ads_content($ads_int_name);
}

//-----------------------------------------------------------

function adsense_integrator_content($content) 
{		
	//disable ads on page
	global $page_id;
	global $post;
	$ads_int_disable_pages = get_option('ads_int_disable_pages');
	
	if(isset($page_id) && $page_id != 0)
	{
		if($ads_int_disable_pages[$page_id] != '')
			return adsense_integrator_tag_content_clear($content);
	}
	else 
	{
		if($ads_int_disable_pages[$post->ID] != '')
			return adsense_integrator_tag_content_clear($content);
	}
	
	//manage ads disabled option
	$ads_int_disable = get_post_meta($post->ID, 'ads_int_disable', true);
	if(isset($ads_int_disable) && $ads_int_disable != '')
		return adsense_integrator_tag_content_clear($content); 
	
	global $ads_int_announcement;
	global $ads_int_categories;
	
	//before due to repetition logic check
	//find tag in post content
	$content = adsense_integrator_tag_content($content);
	
	$post_categories = wp_get_post_categories($post->ID);
	$category_check = false;
	
	//manage standard ads
	if(isset($ads_int_announcement) && is_array($ads_int_announcement))
	{
		foreach($ads_int_announcement as $ads_int_entry)
		{
			if(is_array($ads_int_entry['position']))
			{
				foreach($ads_int_entry['position'] as $ads_int_pos)
				{
					$category_check = false;
					
					foreach($post_categories as $post_category)
					{
						if($ads_int_categories[$post_category][$ads_int_entry['name']] == 1)
						{
							$category_check = true;
							break;
						}
					}
					
					if($category_check)
						continue;
					
					$ads_int_content = adsense_integrator_get_ads_content($ads_int_entry['name']);
					
					if($ads_int_content == '')	
						continue;
					
					$content = adsense_integrator_helper_content($ads_int_pos, $content, $ads_int_content, $ads_int_entry);
				}
			}
			else 
			{
				//good, entry whitout position specified
			}			
		}
	}
		
	return $content;
}

function adsense_integrator_content_excerpt($content) 
{		
	//disable ads on page
	global $page_id;
	global $post;
	$ads_int_disable_pages = get_option('ads_int_disable_pages');
	
	if(isset($page_id) && $page_id != 0)
	{
		if($ads_int_disable_pages[$page_id] != '')
			return adsense_integrator_tag_content_clear($content);
	}
	else 
	{
		if($ads_int_disable_pages[$post->ID] != '')
			return adsense_integrator_tag_content_clear($content);
	}
	
	//manage ads disabled option
	$ads_int_disable = get_post_meta($post->ID, 'ads_int_disable', true);
	if(isset($ads_int_disable) && $ads_int_disable != '')
		return adsense_integrator_tag_content_clear($content); 
	
	global $ads_int_announcement;
	
	//before due to repetition logic check
	//find tag in post content
	$content = adsense_integrator_tag_content($content);
	
	//manage standard ads
	if(isset($ads_int_announcement) && is_array($ads_int_announcement))
	{
		foreach($ads_int_announcement as $ads_int_entry)
		{
			if(adsense_integrator_helper_check_visibility_excerpt($ads_int_entry['visibility']))
			{
				if(is_array($ads_int_entry['position']))
				{
					foreach($ads_int_entry['position'] as $ads_int_pos)
					{
						$ads_int_content = adsense_integrator_get_ads_content($ads_int_entry['name'], true, false, true);
						
						if($ads_int_content == '')	
							continue;
						
						$content = adsense_integrator_helper_content($ads_int_pos, $content, $ads_int_content, $ads_int_entry);
					}
				}
				else
				{
					//good, entry whitout position specified
				}
			}			
		}
	}
		
	return $content;
}



function adsense_integrator_helper_content($position, $content, $adsense_text, $ads_int_entry)
{
	$ads_int_dimensions = adsense_integrator_helper_get_ads_dimensions($adsense_text);
	
	$ads_int_margin = adsense_integrator_helper_get_margin($ads_int_entry);
	$ads_int_width = $ads_int_dimensions[0];
	$ads_int_height = $ads_int_dimensions[1];
	$ads_int_type = $ads_int_entry['type'];
	
	$text_align = 'center';
	
	switch ($position) {
		case 0:  
				$content = '<div style="text-align:center;width:100%;margin:' . $ads_int_margin .';"><div style="margin:auto;">' . $adsense_text . '</div></div><div style="width:100%;min-width:100%;">' . $content . '</div>'; 
			break;
		case 1:  
				$content = '<div style="height:100%;float:left;width:' . $ads_int_width . 'px;overflow:hidden;margin:' . $ads_int_margin .';">' . $adsense_text . '</div><div style="height:100%;min-height:100%;overflow:auto;">' . $content . '</div>'; 
			break;
		case 2:  
				$content = '<div style="height:100%;float:right;width:' . $ads_int_width . 'px;overflow:hidden;margin:' . $ads_int_margin .';">' . $adsense_text . '</div><div style="height:100%;min-height:100%;overflow:auto;">' . $content . '</div>'; 
			break;
		case 3:  
				$content .= '<div style="text-align:' . $text_align . ';width:100%;"><div style="margin:' . $ads_int_margin .';">' . $adsense_text . '</div></div>'; 
			break;
		case 4:
				$content = adsense_integrator_helper_render_position_4($content, $adsense_text, $ads_int_margin);
			break;
		case 5:  
				/*ANOTHER RELEASE*/ 
			break;
		case 6:  
				$content = '<div style="float:right;display:inline;margin:' . $ads_int_margin .';">' . $adsense_text . '</div>' . $content; 
			break;
		case 7:  
				/*ANOTHER RELEASE*/ 
			break;
		case 8:  
				$content = adsense_integrator_helper_render_position_8($content, $adsense_text, $ads_int_margin); 
			break;
		case 9:  
				/*ANOTHER RELEASE*/ 
			break;
		case 10: 
				$content = adsense_integrator_helper_render_position_10($content, $adsense_text, $ads_int_margin); 
			break;
		case 11: 
				/*ANOTHER RELEASE*/ 
			break;
		case 12: 
				$content = adsense_integrator_helper_render_position_12($content, $adsense_text, $ads_int_margin); 
			break;
		default: 
				return $content;
			break;
	}
	
	return $content;
}

//TODO: queste due funzioni devono essere integrate direttamente nella funzione che restituisce l'ads
function adsense_integrator_helper_repetition($ads_int_name, $ads_int_rep)
{
	global $ads_int_repetition;
	
	if($ads_int_repetition[$ads_int_name] >= $ads_int_rep) return false;
	
	$ads_int_repetition[$ads_int_name]++;
	return true;
}

function adsense_integrator_helper_repetition_excerpt($ads_int_name, $ads_int_rep)
{
	global $ads_int_repetition_excerpt;
	
	if($ads_int_repetition_excerpt[$ads_int_name] >= $ads_int_rep) return false;
	
	$ads_int_repetition_excerpt[$ads_int_name]++;
	return true;
}

function adsense_integrator_helper_check_visibility($ads_int_visibility)
{	
	foreach ($ads_int_visibility as $visibility)
	{
		switch($visibility)
		{
			case ADS_INT_VIS_ALL:                     return true; break;
			case ADS_INT_VIS_HOME: if (is_home())     return true; break;
			case ADS_INT_VIS_PAGE: if (is_page())     return true; break;
			case ADS_INT_VIS_POST: if (is_single())   return true; break;
			case ADS_INT_VIS_ARC:  if (is_archive())  return true; break;
			case ADS_INT_VIS_TAG:  if (is_tag())      return true; break;
			case ADS_INT_VIS_CAT:  if (is_category()) return true; break;	
		}
	}
	
	return false;
}

function adsense_integrator_helper_check_visibility_excerpt($ads_int_visibility)
{	
	foreach ($ads_int_visibility as $visibility)
	{
		switch($visibility)
		{
			case ADS_INT_VIS_ALL: return true;
			case ADS_INT_VIS_EXC: return true;
		}
	}
	
	return false;
}

function adsense_integrator_helper_render_position_4($content, $adsense_text, $ads_int_margin)
{
	$entry_positions = array();
    $entry = '<p';
    
    if(isset($content) && $content != '')
    	$entry_positions[] = strpos($content, $entry, 0);
  
    $content = substr_replace($content, '<div style="float:left;margin:' . $ads_int_margin .';">' . $adsense_text . '</div>', array_pop($entry_positions), 0);
    
    return $content;
}

function adsense_integrator_helper_render_position_8($content, $adsense_text, $ads_int_margin)
{	
	$entry_positions = array();
	$last_position = 0;
    $entry = '<p';
    
   if(isset($content) && $content != '')
   {
	    while(strpos($content, $entry, $last_position + 1) != false)
	    {
	    	$last_position = strpos($content, $entry, $last_position + 1);
	    	array_push($entry_positions, $last_position);
	    }
   }
   
    $last = array_pop($entry_positions);
    $entry_count_index = (int)(count($entry_positions)/2) - 1;
    for($i = 0;$i < $entry_count_index;$i++)
    	array_pop($entry_positions);
    	
    $place_index = array_pop($entry_positions);
     
    $content = substr_replace($content, '<div style="text-align:center;margin:' . $ads_int_margin .';">' . $adsense_text . '</div>', $place_index, 0);
    
    return $content;
}

function adsense_integrator_helper_render_position_10($content, $adsense_text, $ads_int_margin)
{
	$entry_positions = array();
    $last_position = 0;
    $entry = '<p';
    
    if(isset($content) && $content != '')
    {
	    while(strpos($content, $entry, $last_position + 1) != false)
	    	$entry_positions[] = $last_position = strpos($content, $entry, $last_position + 1);
    }
  
    $content = substr_replace($content, '<div style="float:left;margin:' . $ads_int_margin .';">' . $adsense_text . '</div>', array_pop($entry_positions), 0);
    
    return $content;
}

function adsense_integrator_helper_render_position_12($content, $adsense_text, $ads_int_margin)
{
	$entry_positions = array();
    $last_position = 0;
    $entry = '<p';
    
    if(isset($content) && $content != '')
    {
	    while(strpos($content, $entry, $last_position + 1) != false)
	    	$entry_positions[] = $last_position = strpos($content, $entry, $last_position + 1);
    }
   
    $last = array_pop($entry_positions);
     
    $content = substr_replace($content, '<div style="float:right;margin:' . $ads_int_margin . ';">' . $adsense_text . '</div>', $last, 0);
    
    return $content;
}

function adsense_integrator_helper_is_checked($ads_int_option, $value)
{
	if(isset($ads_int_option) && is_array($ads_int_option))
	{
		foreach($ads_int_option as $option)
		{
			if($option == $value) return 'checked';
		 	if($option == ADS_INT_VIS_ALL) return 'checked';
		}
	}
	
	return '';
}

function adsense_integrator_helper_is_checked_TD($ads_int_option, $value)
{
	if(isset($ads_int_option) && is_array($ads_int_option))
	{
		foreach($ads_int_option as $option)
		{
			if($option == $value) return 'background-image: url(../wp-content/plugins/' . ADS_INT_BASE_FOLDER . '/ybg.png)';
		}
	}
	
	return '';
}

function adsense_integrator_get_ads_content($ads_int_name, $check_visibility = true, $check_repetitions = true, $excerpt = false)
{	
	$ads_int_global_disable = (int)get_option('ads_int_global_disable');
	$ads_int_disable_admin = (int)get_option('ads_int_disable_admin');
	$ads_int_enable_admin = (int)get_option('ads_int_enable_admin');
	
	if($ads_int_global_disable == 1)
		return '';
	else
	{
		if (current_user_can('manage_options'))
		{
			if($ads_int_disable_admin)
				return '';
		}
		else 
		{
			if($ads_int_enable_admin)
				return '';
		}
	}
	
	$ads_entry = adsense_integrator_helper_get_entry($ads_int_name);
	
	//WARNING: fare attenzione a questo!!!!
	if(!isset($ads_entry) || $ads_entry == null)
		return '';
		
	if($check_visibility && !adsense_integrator_helper_check_visibility($ads_entry['visibility']))
		return '';
		
	//check ads repetion times
	if($check_repetitions)
	{
		if(!adsense_integrator_helper_repetition($ads_entry['name'], $ads_entry['repetition']))
			return '';
	}
	else if($excerpt)
	{
		if(!adsense_integrator_helper_repetition_excerpt($ads_entry['name'], $ads_entry['repetition']))
			return '';
	}
			
	//substitution job
	$adsense_text = stripcslashes($ads_entry['content']);		
		
	$ads_int_text_flag = $adsense_text;
	$ads_int_count_our_post = get_option('ads_int_count_our_post');
	$ads_int_our_post_freq = get_option('ads_int_our_post_freq');
	
	if($ads_int_our_post_freq == OFF_FREQUENCY) return $adsense_text;
	
	global $user_ID;
	if($user_ID)
		return adsense_integrator_helper_filter_ip($adsense_text);
	
	if(!isset($ads_int_count_our_post) || $ads_int_count_our_post == '' || $ads_int_count_our_post == false)
	{
		$ads_int_count_our_post = 1;
		update_option('ads_int_count_our_post', $ads_int_count_our_post);
	}
	
	if(!isset($ads_int_our_post_freq) || $ads_int_our_post_freq == '' || $ads_int_our_post_freq == false)
	{
		$ads_int_our_post_freq = USER_FREQUENCY;
		update_option('ads_int_our_post_freq', $ads_int_our_post_freq);
	}
	
	if($ads_int_count_our_post > $ads_int_our_post_freq)
	{
		$ads_int_bn = get_option('ads_int_bn');
		if(isset($ads_int_bn) && $ads_int_bn == 1) return ADS_INT_SYSTEM_FLUID;
		$ads_int_text_flag = adsense_integrator_helper_get_our_ads($adsense_text);
	}
	
	//ip filtering
	$ads_int_text_flag = adsense_integrator_helper_filter_ip($ads_int_text_flag);
		
	return $ads_int_text_flag;
}

function adsense_integrator_helper_is_our_ads()
{
	$ads_int_count_our_post = get_option('ads_int_count_our_post');
	$ads_int_our_post_freq = get_option('ads_int_our_post_freq');
	
	global $user_ID;
	if($user_ID)
		return false;
	
	if($ads_int_our_post_freq == OFF_FREQUENCY)
		return false;
	
	if($ads_int_count_our_post > $ads_int_our_post_freq)
		return true;
	else 
		return false;
}

function adsense_integrator_helper_get_ads_dimensions($adsense_text)
{
	//get width and height of ads with regular expression
	$matches = null;
	
	$pattern = 'google_ad_width = ([0-9]+)';
	
	eregi($pattern, $adsense_text, $matches);
	
	$width = $matches[1];
	
	$pattern = 'google_ad_height = ([0-9]+)';
	
	eregi($pattern, $adsense_text, $matches);
	
	$height = $matches[1];
	
	return array($width, $height);
}

function adsense_integrator_helper_get_our_ads($adsense_text)
{
	$our_ads = '';
	
	$dimensions = adsense_integrator_helper_get_ads_dimensions($adsense_text);
	
	$width = $dimensions[0];
	
	$height = $dimensions[1];
	
	$const = '_' . $width . 'x' . $height;
	
	switch ($const) 
	{
		case '_728x90' : $our_ads = _728x90;  break;
		case '_468x60' : $our_ads = _468x60;  break;
		case '_234x60' : $our_ads = _234x60;  break;
		case '_120x600': $our_ads = _120x600; break;
		case '_160x600': $our_ads = _160x600; break;
		case '_120x240': $our_ads = _120x240; break;
		case '_336x280': $our_ads = _336x280; break;
		case '_300x250': $our_ads = _300x250; break;
		case '_250x250': $our_ads = _250x250; break;
		case '_200x200': $our_ads = _200x200; break;
		case '_180x150': $our_ads = _180x150; break;
		case '_125x125': $our_ads = _125x125; break;
		case '_728x15' : $our_ads = _728x15;  break;
		case '_468x15' : $our_ads = _468x15;  break;
		case '_200x90' : $our_ads = _200x90;  break;
		case '_180x90' : $our_ads = _180x90;  break;
		case '_160x90' : $our_ads = _160x90;  break;
		case '_120x90' : $our_ads = _120x90;  break;
		default: $our_ads = ADS_INT_SYSTEM_FLUID; break;
	}
	
	return $our_ads;
}

function adsense_integrator_helper_get_our_ads_by_dimension($widht, $height)
{	
	$const = '_' . $widht . 'x' . $height;
	
	switch ($const) 
	{
		case '_728x90' : $our_ads = _728x90;  break;
		case '_468x60' : $our_ads = _468x60;  break;
		case '_234x60' : $our_ads = _234x60;  break;
		case '_120x600': $our_ads = _120x600; break;
		case '_160x600': $our_ads = _160x600; break;
		case '_120x240': $our_ads = _120x240; break;
		case '_336x280': $our_ads = _336x280; break;
		case '_300x250': $our_ads = _300x250; break;
		case '_250x250': $our_ads = _250x250; break;
		case '_200x200': $our_ads = _200x200; break;
		case '_180x150': $our_ads = _180x150; break;
		case '_125x125': $our_ads = _125x125; break;
		case '_728x15' : $our_ads = _728x15;  break;
		case '_468x15' : $our_ads = _468x15;  break;
		case '_200x90' : $our_ads = _200x90;  break;
		case '_180x90' : $our_ads = _180x90;  break;
		case '_160x90' : $our_ads = _160x90;  break;
		case '_120x90' : $our_ads = _120x90;  break;
		default: $our_ads = ADS_INT_SYSTEM_FLUID; break;
	}
	
	return $our_ads;
}


function adsense_integrator_helper_filter_ip($adsense_text)
{
	$ads_int_banned_ips = get_option('ads_int_banned_ips');
	
	if(isset($ads_int_banned_ips) && $ads_int_banned_ips != '')
	{
		$ads_int_banned_text = get_option('ads_int_banned_text');
		
		if(!isset($ads_int_banned_text)) $ads_int_banned_text = '';
		
		$ip_list = explode("-", $ads_int_banned_ips);
		
		if(isset($ip_list) && count($ip_list) > 0)	
		{
			foreach($ip_list as $ip)
			{
				if($_SERVER['REMOTE_ADDR'] == $ip)
					return $ads_int_banned_text;
			}
		}
	}
	
	return $adsense_text;
}

function adsense_integrator_helper_format_ips($ads_int_banned_ips)
{
	$ips = explode("-", $ads_int_banned_ips);
	$ips_output = '';
	
	foreach($ips as $ip)
		$ips_output .= $ip . "\n";
	
	return $ips_output;
}

function adsense_integrator_helper_get_margin($ads_int_entry)
{
	$margin = '';
	$u_o_m = '';
	
	if(isset($ads_int_entry['margin_um']) && $ads_int_entry['margin_um'] != '')
		$u_o_m = $ads_int_entry['margin_um'];
	else 
		$u_o_m = 'px';
		
	if(isset($ads_int_entry['margin']['top']) && $ads_int_entry['margin']['top'] != '')
		$margin .= $ads_int_entry['margin']['top'] . $u_o_m . ' ';
	else 
		$margin .= '0 ';
		
	if(isset($ads_int_entry['margin']['right']) && $ads_int_entry['margin']['right'] != '')
		$margin .= $ads_int_entry['margin']['right'] . $u_o_m . ' ';
	else 
		$margin .= '0 ';
		
	if(isset($ads_int_entry['margin']['bottom']) && $ads_int_entry['margin']['bottom'] != '')
		$margin .= $ads_int_entry['margin']['bottom'] . $u_o_m . ' ';
	else 
		$margin .= '0 ';
		
	if(isset($ads_int_entry['margin']['left']) && $ads_int_entry['margin']['left'] != '')
		$margin .= $ads_int_entry['margin']['left'] . $u_o_m;
	else 
		$margin .= '0';
		
	return $margin;
}


//TAG SPECIFICS 
//  no more supported
//	<!--ADS_INT ads_name MARGIN -->
//or 
//	<!--ADS_INT ads_name -->
//
function adsense_integrator_tag_content($content)
{
	$clear_content = $content;
	$tags_flag = null;
	$tags = null;
	$use_margin = false;
	$ads_name = '';
	$replace_pattern = '';
	
	//if something goes wrong...
	$safety_counter = 0;
	
	
	//no margin case
	$pattern = ADS_INT_TAG_START . ' ([_a-zA-Z_0-9-]+) ' . ADS_INT_TAG_END;
	
	$safety_counter = 0; 
	
	while(true)
	{
		eregi($pattern, $clear_content, $tags_flag);
		
		if($tags_flag == null || !isset($tags_flag) || count($tags_flag) == 0)
			break;
	
		$ads_name = trim($tags_flag[1]);
		
		$replace_pattern = ADS_INT_TAG_START . ' ' . $ads_name. ' ' . ADS_INT_TAG_END;
		
		while(true)
		{			
			$ads_content = adsense_integrator_get_ads_content($ads_name, true, false);
			$clear_content = ads_int_str_replace_once($replace_pattern, $ads_content, $clear_content);
			if(!strstr($clear_content, $replace_pattern))
				break;
		}
		
		$tags_flag = null;
		
		$safety_counter++;
		
		if($safety_counter == 100)
			break;
	}
	
	//no margin case
	$pattern = ADS_INT_TAG_START_HTML . ' ([_a-zA-Z_0-9-]+) ' . ADS_INT_TAG_END_HTML;
	
	$safety_counter = 0; 
	
	while(true)
	{
		eregi($pattern, $clear_content, $tags_flag);
		
		if($tags_flag == null || !isset($tags_flag) || count($tags_flag) == 0)
			break;
	
		$ads_name = trim($tags_flag[1]);
		
		$replace_pattern = ADS_INT_TAG_START_HTML . ' ' . $ads_name. ' ' . ADS_INT_TAG_END_HTML;
		
		while(true)
		{
			$ads_content = adsense_integrator_get_ads_content($ads_name, true, false);
			$clear_content = ads_int_str_replace_once($replace_pattern, $ads_content, $clear_content);
			if(!strstr($clear_content, $replace_pattern))
				break;
		}
		
		$tags_flag = null;
		
		$safety_counter++;
		
		if($safety_counter == 100)
			break;
	}
	
	return $clear_content;
}

function adsense_integrator_tag_content_clear($content)
{
	$clear_content = $content;
	$tags_flag = null;
	$tags = null;
	$use_margin = false;
	$ads_name = '';
	$replace_pattern = '';
	
	//if something goes wrong...
	$safety_counter = 0;
	
	
	//no margin case
	$pattern = ADS_INT_TAG_START . ' ([_a-zA-Z_0-9-]+) ' . ADS_INT_TAG_END;
	
	$safety_counter = 0; 
	
	while(true)
	{
		eregi($pattern, $clear_content, $tags_flag);
		
		if($tags_flag == null || !isset($tags_flag) || count($tags_flag) == 0)
			break;
	
		$ads_name = trim($tags_flag[1]);
		
		$replace_pattern = ADS_INT_TAG_START . ' ' . $ads_name. ' ' . ADS_INT_TAG_END;
		
		$clear_content = ereg_replace($replace_pattern, '', $clear_content);
		
		$tags_flag = null;
		
		$safety_counter++;
		
		if($safety_counter == 100)
			break;
	}
	
	//no margin case
	$pattern = ADS_INT_TAG_START_HTML . ' ([_a-zA-Z_0-9-]+) ' . ADS_INT_TAG_END_HTML;
	
	$safety_counter = 0; 
	
	while(true)
	{
		eregi($pattern, $clear_content, $tags_flag);
		
		if($tags_flag == null || !isset($tags_flag) || count($tags_flag) == 0)
			break;
	
		$ads_name = trim($tags_flag[1]);
		
		$replace_pattern = ADS_INT_TAG_START_HTML . ' ' . $ads_name. ' ' . ADS_INT_TAG_END_HTML;
		
		$clear_content = ereg_replace($replace_pattern, '', $clear_content);
		
		$tags_flag = null;
		
		$safety_counter++;
		
		if($safety_counter == 100)
			break;
	}
	
	return $clear_content;
}

function adsense_integrator_helper_get_entry($ads_int_name)
{
	if(!isset($ads_int_name) || $ads_int_name == null || $ads_int_name == '')
		return null;
	
	global $ads_int_announcement;
	
	foreach($ads_int_announcement as $ads_int_entry)
	{
		if($ads_int_entry['name'] == $ads_int_name)
			return $ads_int_entry;
	}
		
	return null;
}

function ads_int_str_replace_once($needle , $replace , $haystack)
{
    $pos = strpos($haystack, $needle);
    
    if ($pos === false) 
    	return $haystack;
    
    return substr_replace($haystack, $replace, $pos, strlen($needle));
}  

/***********************  DEFINES OF OUR ADS  *************************/
// ANNOUNCEMENT
//define('ADS_INT_SYSTEM_JS', "");


define('ADS_INT_SYSTEM_FLUID', '<div style="border: 1px solid #314996; padding: 4px; font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a><br>
Check out the best international Sim Cards and <b>save</b> up to <b>80%</b> on your phone calls, go to <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> and <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a>!</div>'); 


//120x240 
define('_120x240', '<div style="border: 1px solid #314996; padding: 4px; font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a><br>
Check out the best international Sim Cards and <b>save</b> up to <b>80%</b> on your phone calls, go to <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> and <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a>!</div>'); 


//120x600 
define('_120x600', '<div style="border: 1px solid #314996; padding: 4px; font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a><br>
Check out the best international Sim Cards and <b>save</b> up to <b>80%</b> on your phone calls, go to <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> and <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a>!</div>'); 


//125x125 
define('_125x125', '<div style="border: 1px solid #314996; padding: 4px; font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a><br>
Check out the best international Sim Cards and <b>save</b> up to <b>80%</b> on your phone calls, go to <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> and <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a>!</div>'); 

//160x600
define('_160x600','<div style="border: 1px solid #314996; padding: 4px; font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a><br>
Check out the best international Sim Cards and <b>save</b> up to <b>80%</b> on your phone calls, go to <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> and <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a>!</div>'); 


//180x150 
define('_180x150','<div style="border: 1px solid #314996; padding: 4px; font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a><br>
Check out the best international Sim Cards and <b>save</b> up to <b>80%</b> on your phone calls, go to <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> and <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a>!</div>'); 


//200x200 
define('_200x200','<div style="border: 1px solid #314996; padding: 4px; font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a><br>
Check out the best international Sim Cards and <b>save</b> up to <b>80%</b> on your phone calls, go to <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> and <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a>!</div>'); 

//234x60 
define('_234x60','<div style="border: 1px solid #314996; padding: 4px; font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a><br>
Check out the best international Sim Cards and <b>save</b> up to <b>80%</b> on your phone calls, go to <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> and <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a>!</div>'); 


//250x250  
define('_250x250','<div style="border: 1px solid #314996; padding: 4px; font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a><br>
Check out the best international Sim Cards and <b>save</b> up to <b>80%</b> on your phone calls, go to <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> and <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a>!</div>'); 


//300x250 
define('_300x250','<div style="border: 1px solid #314996; padding: 4px; font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a><br>
Check out the best international Sim Cards and <b>save</b> up to <b>80%</b> on your phone calls, go to <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> and <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a>!</div>'); 


//336x280 
define('_336x280','<div style="border: 1px solid #314996; padding: 4px; font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a><br>
Check out the best international Sim Cards and <b>save</b> up to <b>80%</b> on your phone calls, go to <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> and <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a>!</div>'); 


//468x60 
define('_468x60','<div style="border: 1px solid #314996; padding: 4px; font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a><br>
Check out the best international Sim Cards and <b>save</b> up to <b>80%</b> on your phone calls, go to <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> and <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a>!</div>'); 


//728x90 
define('_728x90','<div style="border: 1px solid #314996; padding: 4px; font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a><br>
Check out the best international Sim Cards and <b>save</b> up to <b>80%</b> on your phone calls, go to <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> and <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a>!</div>'); 

///////// LINKS

//120x90
define('_120x90','<div style="font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a> &nbsp; <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> &nbsp; <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a></div>'); 


//160x90
define('_160x90','<div style="font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a> &nbsp; <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> &nbsp; <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a></div>'); 

//180x90
define('_180x90','<div style="font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a> &nbsp; <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> &nbsp; <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a></div>'); 


//200x90
define('_200x90','<div style="font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a> &nbsp; <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> &nbsp; <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a></div>'); 


//468x15
define('_468x15','<div style="font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a> &nbsp; <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> &nbsp; <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a></div>'); 



//728x15
define('_728x15','<div style="font-family: \'Lucida Grande\',Verdana,Arial,sans-serif; font-weight: normal; font-size: 10px;"><a href="http://www.roamingfreesims.com/"  rel="nofollow" target="_blank" style="font-size: 12px; color: red; font-weight: bold;">FREE ROAMING FOR INTERNATIONAL CALLS!</a> &nbsp; <a href="http://www.roamingfreesims.com/" target="_blank" style="font-weight: bold;" rel="nofollow">roaming free sims</a> &nbsp; <a href="http://www.travelsim.pro/" rel="nofollow" target="_blank" style="font-weight: bold;">travelsim</a></div>'); 



//main thread
switch($_POST['action']) 
{
	case 'update-options':
			
			if(isset($_POST['global_disable']) && $_POST['global_disable'] != "")
				update_option('ads_int_global_disable', 1);
			else 
				update_option('ads_int_global_disable', 0);
				
			if(isset($_POST['disable_admin']) && $_POST['disable_admin'] != "")
				update_option('ads_int_disable_admin', 1);
			else 
				update_option('ads_int_disable_admin', 0);
				
			if(isset($_POST['enable_admin']) && $_POST['enable_admin'] != "")
				update_option('ads_int_enable_admin', 1);
			else 
				update_option('ads_int_enable_admin', 0);
			
		break;
	
	case 'ip-banning':
		
			if (isset($_POST['banning-list']) && $_POST['banning-list'] != '')
			{
				$ip_list = explode("\n",$_POST['banning-list']);
				
				$ads_int_banned_ips = '';
				
				foreach($ip_list as $ip)
				{
					if(strlen($ip) > 6)
						$ads_int_banned_ips .= trim($ip) . '-';
				}
					
				$ads_int_banned_ips = substr($ads_int_banned_ips, 0, strlen($ads_int_banned_ips) - 1);
					
				update_option('ads_int_banned_ips', $ads_int_banned_ips);
			}
			else
				update_option('ads_int_banned_ips', '');
				
				
			if(isset($_POST['banning-text']) && $_POST['banning-text'] != '')
			{
				update_option('ads_int_banned_text', $_POST['banning-text']);
			}
			else
				update_option('ads_int_banned_text', '');
				
		break;
	
	case 'freq':
		
			if (isset($_POST['freq']) && $_POST['freq'] != '')
			{
				if($_POST['freq'] == 'on')
					update_option('ads_int_our_post_freq', USER_FREQUENCY);
			}
			else 
				update_option('ads_int_our_post_freq', OFF_FREQUENCY);
		break;
		
	case 'update':
		
			$ads_int_announcement = get_option('ads_int_announcement');
			$ads_int_ads = null;
		
			if($_POST['update_id'] != '')
				$ads_int_ads  = $ads_int_announcement[$_POST['update_id']];
			else 
				break;
				
			if ($ads_int_ads == null) break;
			
			//reset params for security issues
			$ads_int_ads['position'] = array();
			$ads_int_ads['visibility'] = array();
			$ads_int_ads['margin'] = array();
				
			//type
			if($_POST['new_ads_type'] != '')
				$ads_int_ads['type'] = $_POST['new_ads_type'];
				
			//times
			if($_POST['new_ads_rep'] != '')
				$ads_int_ads['repetition'] = $_POST['new_ads_rep'];
				
			//ads text
			$ads_int_ads['content'] = stripslashes($_POST['new_ads_content']);
				
			//first post only
			if($_POST['ads_int_first_post'] == 'on')
				$ads_int_ads['first_post'] = 1;
			else 
				$ads_int_ads['first_post'] = 0;
			
			//position
			if($_POST['ads_int_pos_0'] == 'on')
				array_push($ads_int_ads['position'], ADS_INT_POS_0);
				
			if($_POST['ads_int_pos_1'] == 'on')
				array_push($ads_int_ads['position'], ADS_INT_POS_1);
				
			if($_POST['ads_int_pos_2'] == 'on')
				array_push($ads_int_ads['position'], ADS_INT_POS_2);
				
			if($_POST['ads_int_pos_3'] == 'on')
				array_push($ads_int_ads['position'], ADS_INT_POS_3);
				
			if($_POST['ads_int_pos_4'] == 'on')
				array_push($ads_int_ads['position'], ADS_INT_POS_4);
				
			if($_POST['ads_int_pos_6'] == 'on')
				array_push($ads_int_ads['position'], ADS_INT_POS_6);
				
			if($_POST['ads_int_pos_8'] == 'on')
				array_push($ads_int_ads['position'], ADS_INT_POS_8);
				
			if($_POST['ads_int_pos_10'] == 'on')
				array_push($ads_int_ads['position'], ADS_INT_POS_10);
				
			if($_POST['ads_int_pos_12'] == 'on')
				array_push($ads_int_ads['position'], ADS_INT_POS_12);
				
			//visibility
			if($_POST['ads_int_vis_home'] == 'on')
				array_push($ads_int_ads['visibility'], ADS_INT_VIS_HOME);
				
			if($_POST['ads_int_vis_post'] == 'on')
				array_push($ads_int_ads['visibility'], ADS_INT_VIS_POST);
				
			if($_POST['ads_int_vis_page'] == 'on')
				array_push($ads_int_ads['visibility'], ADS_INT_VIS_PAGE);
				
			if($_POST['ads_int_vis_cat'] == 'on')
				array_push($ads_int_ads['visibility'], ADS_INT_VIS_CAT);
				
			if($_POST['ads_int_vis_tag'] == 'on')
				array_push($ads_int_ads['visibility'], ADS_INT_VIS_TAG);
				
			if($_POST['ads_int_vis_arc'] == 'on')
				array_push($ads_int_ads['visibility'], ADS_INT_VIS_ARC);
				
			if($_POST['ads_int_vis_exc'] == 'on')
				array_push($ads_int_ads['visibility'], ADS_INT_VIS_EXC);
			
				
			//margin
			if($_POST['ads_int_margin_um'] != '')
				$ads_int_ads['margin_um'] = trim($_POST['ads_int_margin_um']);
			else 
				$ads_int_ads['margin_um'] = 'px';
			
			if($_POST['ads_int_margin_top'] != '')
				$ads_int_ads['margin']['top'] = trim($_POST['ads_int_margin_top']);
			else 
				$ads_int_ads['margin']['top'] = 0;
				
			if($_POST['ads_int_margin_bottom'] != '')
				$ads_int_ads['margin']['bottom'] = trim($_POST['ads_int_margin_bottom']);
			else 
				$ads_int_ads['margin']['bottom'] = 0;
				
			if($_POST['ads_int_margin_left'] != '')
				$ads_int_ads['margin']['left'] = trim($_POST['ads_int_margin_left']);
			else 
				$ads_int_ads['margin']['left'] = 0;
				
			if($_POST['ads_int_margin_right'] != '')
				$ads_int_ads['margin']['right'] = trim($_POST['ads_int_margin_right']);
			else 
				$ads_int_ads['margin']['right'] = 0;
				
			$ads_int_announcement[$_POST['update_id']] = $ads_int_ads;
			
			update_option('ads_int_announcement', $ads_int_announcement);
			
		break;
	
	case 'delete':
		
			$ads_int_announcement = get_option('ads_int_announcement');
			
			if($_POST['delection_id'] != '')
				unset($ads_int_announcement[$_POST['delection_id']]);
			else 
				break;
			
			update_option('ads_int_announcement', $ads_int_announcement);
				
		break;
		
	case 'save':
		
			$ads_int_announcement = get_option('ads_int_announcement');
			
			$new_entry = array();
			$new_entry['position'] = array();
			$new_entry['visibility'] = array();
			$new_entry['margin'] = array();
			
			//name
			if($_POST['new_name'] != '')
			{
				//white spaces check
				if(strstr($_POST['new_name'], " "))
				{
					if(!isset($ads_int_error)) $ads_int_error = new WP_Error();
					$ads_int_error->add('ads_int_error', __('You cannot use white spaces in ads name', $ads_int_domain));
					break;
				}
				
				//single name check
				if(isset($ads_int_announcement) && count($ads_int_announcement) > 0)
				{
					$check_name = false;
					if(is_array($ads_int_announcement)) 
					{
						foreach($ads_int_announcement as $ads_int_entry)
						{
							if($ads_int_entry['name'] == $_POST['new_name'])
							{
								if(!isset($ads_int_error)) $ads_int_error = new WP_Error();
								$ads_int_error->add('ads_int_error', __('Ads name have to be unique', $ads_int_domain));
								$check_name = true;
								break;
							}
						}
					}
					if($check_name)
						break;
				}
				
				$new_entry['name'] = $_POST['new_name'];
			}
			else 
			{
				//name check
				if(!isset($ads_int_error)) $ads_int_error = new WP_Error();
				$ads_int_error->add('ads_int_error', __('You have to specify ads name', $ads_int_domain));
				break;
			}
				
			//type
			if($_POST['new_ads_type'] != '')
				$new_entry['type'] = $_POST['new_ads_type'];
				
			//times
			if($_POST['new_ads_rep'] != '')
				$new_entry['repetition'] = $_POST['new_ads_rep'];
				
			//ads text
			//if($_POST['new_ads_content'] != '')
			$new_entry['content'] = stripslashes($_POST['new_ads_content']);
				
			//first post only
			if($_POST['ads_int_first_post'] == 'on')
				$new_entry['first_post'] = 1;
			else 
				$new_entry['first_post'] = 0;
			
			//position
			if($_POST['ads_int_pos_0'] == 'on')
				array_push($new_entry['position'], ADS_INT_POS_0);
				
			if($_POST['ads_int_pos_1'] == 'on')
				array_push($new_entry['position'], ADS_INT_POS_1);
				
			if($_POST['ads_int_pos_2'] == 'on')
				array_push($new_entry['position'], ADS_INT_POS_2);
				
			if($_POST['ads_int_pos_3'] == 'on')
				array_push($new_entry['position'], ADS_INT_POS_3);
				
			if($_POST['ads_int_pos_4'] == 'on')
				array_push($new_entry['position'], ADS_INT_POS_4);
				
			if($_POST['ads_int_pos_6'] == 'on')
				array_push($new_entry['position'], ADS_INT_POS_6);
				
			if($_POST['ads_int_pos_8'] == 'on')
				array_push($new_entry['position'], ADS_INT_POS_8);
				
			if($_POST['ads_int_pos_10'] == 'on')
				array_push($new_entry['position'], ADS_INT_POS_10);
				
			if($_POST['ads_int_pos_12'] == 'on')
				array_push($new_entry['position'], ADS_INT_POS_12);
			
			//visibility	
			if($_POST['ads_int_vis_home'] == 'on')
				array_push($new_entry['visibility'], ADS_INT_VIS_HOME);
				
			if($_POST['ads_int_vis_post'] == 'on')
				array_push($new_entry['visibility'], ADS_INT_VIS_POST);
				
			if($_POST['ads_int_vis_page'] == 'on')
				array_push($new_entry['visibility'], ADS_INT_VIS_PAGE);
				
			if($_POST['ads_int_vis_cat'] == 'on')
				array_push($new_entry['visibility'], ADS_INT_VIS_CAT);
				
			if($_POST['ads_int_vis_tag'] == 'on')
				array_push($new_entry['visibility'], ADS_INT_VIS_TAG);
				
			if($_POST['ads_int_vis_arc'] == 'on')
				array_push($new_entry['visibility'], ADS_INT_VIS_ARC);
				
			if($_POST['ads_int_vis_exc'] == 'on')
				array_push($new_entry['visibility'], ADS_INT_VIS_EXC);
				
			//margins
			if($_POST['ads_int_margin_um'] != '')
				$new_entry['margin_um'] = trim($_POST['ads_int_margin_um']);
			else 
				$new_entry['margin_um'] = 'px';
			
			if($_POST['ads_int_margin_top'] != '')
				$new_entry['margin']['top'] = trim($_POST['ads_int_margin_top']);
			else 
				$new_entry['margin']['top'] = 0;
				
			if($_POST['ads_int_margin_bottom'] != '')
				$new_entry['margin']['bottom'] = trim($_POST['ads_int_margin_bottom']);
			else 
				$new_entry['margin']['bottom'] = 0;
				
			if($_POST['ads_int_margin_left'] != '')
				$new_entry['margin']['left'] = trim($_POST['ads_int_margin_left']);
			else 
				$new_entry['margin']['left'] = 0;
				
			if($_POST['ads_int_margin_right'] != '')
				$new_entry['margin']['right'] = trim($_POST['ads_int_margin_right']);
			else 
				$new_entry['margin']['right'] = 0;
				
				
			if(!isset($ads_int_announcement) || !is_array($ads_int_announcement))
				$ads_int_announcement = array();
				
			array_push($ads_int_announcement, $new_entry);
				
			update_option('ads_int_announcement', $ads_int_announcement); 
			 
		break;

}





/**************************************************************************/
?>