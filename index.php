<?php
/* 
Plugin Name: WPoniOS - REST API
Plugin URI: http://www.jesuisdeveloppeur.com
Description: <b>WPoniOS - Rest API</b> is a plugin which provide a REST interface in WordPress CMS.
Author: Vincent Saluzzo
Version: 1.0
License: Copyright 2012 Vincent Saluzzo

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/

require_once('wponios.php');
require_once("Slim/Slim.php");
add_filter('generate_rewrite_rules', 'eur_flush_rules');
add_action('template_redirect', 'eur_redirect');
register_activation_hook(__FILE__, 'wponios_add_default');
add_action('admin_menu', 'wponios_menu');
add_action('admin_init', 'wponios_init');	

function wponios_add_default() {
	$tmp = get_option('wponios_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('wponios_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(	
				"custom_url_prefix" => "wponios",
				"debug" => "0"
		);
		update_option('wponios_options', $arr);
	}
	
	$tmp2 = get_option('wponios_methods_options');
	if(($tmp2['chk_default_options_db'] == '1') || (!is_array($tmp2))) {
		delete_option('wponios_methods_options');
		$arr = array( 
				"getposts" => "1",
				"getpost" => "1",
				"getallcommentsonpost" => '1',
				"getcommentonpost" => '1',
				"getpages" => "1",
				"getpage" => "1",
				"getallcommentsonpage" => '1',
				"getcommentonpage" => '1',
				"getcategories" => '1',
				"gettags" => '1',
				"putcommentonpost" => '1',
				"putcommentonpage" => '1'
		);
		update_option('wponios_methods_options', $arr);
	}
}

function wponios_init() {
	register_setting("wponios_plugin_options", "wponios_options");
	register_setting("wponios_plugin_methods_options", "wponios_methods_options"); 
}

function wponios_menu() {
	add_menu_page("WPoniOS Rest API - General Settings", "WPoniOS", "manage_options", "wponios_restapi", "wponios_menu_render");
	add_submenu_page("wponios_restapi", "WPoniOS Rest API - Activated/Desactivated Methods", "Activated Methods", "manage_options", "wponios_restapi_methods", "wponios_menu_render_methods");
	add_submenu_page("wponios_restapi", "WPoniOS Rest API - About", "About", "manage_options", "wponios_restapi_about", "wponios_menu_render_about");
	
}

function wponios_menu_render() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	?>
	<div class="wrap">
		<h2>WPoniOS Rest API - General Settings</h2>
		<h4>This page allow you to set the different options four the WPoniOS Rest API Plugin. By default, the custom URL prefix are set to "wponios" and the debug mode is deactivated, but if you need a customization, you can change this options.</h4>
		<form method="post" action="options.php">
		<?php settings_fields('wponios_plugin_options');  ?> 
		<?php $options = get_option("wponios_options"); ?>
		
		<table class="form-table">
			<tr>
				<th scope="row">Custom URL Prefix (http://..url.tld/<span style="font-weight: bold;">PREFIX</span>/)</th>
				<td>
					<input type="text" size="57" name="wponios_options[custom_url_prefix]" value="<?php echo $options['custom_url_prefix']; ?>" /><span style="color:#666666;margin-left:32px; font-style: italic;"><em>(default: "wponios", if blank, the plugin take automatically the default value)</em></span>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row">Activate the Debug mode</th>
				<td>
					<input name="wponios_options[debug]" type="checkbox" value="1" <?php if (isset($options['debug'])) { checked('1', $options['debug']); } ?> /><br />
				</td>
			</tr>
		</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="Save Changes"/>
			</p>
		</form>
	</div>
	<div class="wrap" style="text-align: center;margin: 10%; border: 1px solid #333; padding: 10px; -webkit-border-radius: 10px; -moz-border-radius: 10px;">
		<h2>You want/need an application of your blog on Smartphone ?</h2>
		<h2>Contact me direclty at <a href="mailto:vincentsaluzzo@me.com">vincentsaluzzo@me.com</a> or by my website: <a href="http://www.jesuisdeveloppeur.com">http://www.jesuisdeveloppeur.com</a></h2>
	</div>
	<?php
}

function wponios_menu_render_methods() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	?>
	<div class="wrap">
		<h2>WPoniOS Rest API - Activate/Deactivate REST Methods</h2>
		<form method="post" action="options.php">
		<?php settings_fields("wponios_plugin_methods_options"); ?>
		<?php $options = get_option("wponios_methods_options")  ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"> <h3>Post methods</h3></th>
				<td>
					<table>
						<tr>
							<td>
								<label><input name="wponios_methods_options[getposts]" type="checkbox" value="1" <?php if (isset($options['getposts'])) { checked('1', $options['getposts']); } ?>/> All Posts </label>
							</td>
							<td>
								<em style="margin-left:32px">(URI template: GET /posts)</em>
							</td>
						</tr>
						<tr>
							<td><label><input name="wponios_methods_options[getpost]" type="checkbox" value="1" <?php if (isset($options['getpost'])) { checked('1', $options['getpost']); } ?>/> Single Post</label>
							</td>
							<td>
								<em style="margin-left:32px">(URI template: GET /post/:post_id)</em>
							</td>
						</tr>
						<tr>
							<td>
								 <label><input name="wponios_methods_options[getallcommentsonpost]" type="checkbox" value="1" <?php if (isset($options['getallcommentsonpost'])) { checked('1', $options['getallcommentsonpost']); } ?>/> All Comments on Single Post</label>
							</td>
							<td>
								<em style="margin-left:32px">(URI template: GET /post/:post_id/comments)</em>
							</td>
						</tr>
						<tr>
							<td>
								<label><input name="wponios_methods_options[getcommentonpost]" type="checkbox" value="1" <?php if (isset($options['getcommentonpost'])) { checked('1', $options['getcommentonpost']); } ?>/> Single Comment on Single Post </label>
							</td>
							<td>
								<em style="margin-left:32px">(URI template: GET /post/:post_id/comment/:comment_id)</em>
							</td>
						</tr>
						<tr>
							<td>
								<label><input name="wponios_methods_options[putcommentonpost]" type="checkbox" value="1" <?php if (isset($options['putcommentonpost'])) { checked('1', $options['putcommentonpost']); } ?>/> Put Comment on Single Post </label>
							</td>
							<td>
								<em style="margin-left:32px">(URI template: PUT /post/:post_id/comment)</em
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"> <h3>Page methods</h3></th>
				<td>
					<table>
						<tr>
							<td>
								<label><input name="wponios_methods_options[getpages]" type="checkbox" value="1" <?php if (isset($options['getpages'])) { checked('1', $options['getpages']); } ?>/> All Pages </label>
							</td>
							<td>
								<em style="margin-left:32px">(URI template: GET /pages)</em>
							</td>
						</tr>
						<tr>
							<td>
								<label><input name="wponios_methods_options[getpage]" type="checkbox" value="1" <?php if (isset($options['getpage'])) { checked('1', $options['getpage']); } ?>/> Single Page </label>
							</td>
							<td>
								<em style="margin-left:32px">(URI template: GET /page/:page_id)</em>
							</td>
						</tr>
						<tr>
							<td>
								<label><input name="wponios_methods_options[getallcommentsonpage]" type="checkbox" value="1" <?php if (isset($options['getallcommentsonpage'])) { checked('1', $options['getallcommentsonpage']); } ?>/> All Comments on Single Page 
							</td>
							<td>
								<em style="margin-left:32px">(URI template: GET /page/:page_id/comments)</em>	
							</td>
						</tr>
						<tr>
							<td>
								<label><input name="wponios_methods_options[getcommentonpage]" type="checkbox" value="1" <?php if (isset($options['getcommentonpage'])) { checked('1', $options['getcommentonpage']); } ?>/> Single Comment on Single Page</label>
							</td>
							<td>
								<em style="margin-left:32px">(URI template: GET /page/:page_id/comment/:comment_id)</em>
							</td>
						</tr>
						<tr>
							<td>
								<label><input name="wponios_methods_options[putcommentonpage]" type="checkbox" value="1" <?php if (isset($options['putcommentonpage'])) { checked('1', $options['putcommentonpage']); } ?>/> Put Comment on Single Page </label>
							</td>
							<td>
								<em style="margin-left:32px">(URI template: PUT /page/:page_id/comment)</em>
							</td>
						</tr>
					</table>
				</td>
					
			</tr>
			
			<tr valign="top">
				<th scope="row"> <h3>Category methods</h3></th>
				<td>
					<table>		
						<tr>
							<td>
								<label><input name="wponios_methods_options[getcategories]" type="checkbox" value="1" <?php if (isset($options['getcategories'])) { checked('1', $options['getcategories']); } ?>/> All Categories</label>
							</td>
							<td>
								<em style="margin-left:32px">(URI template: GET /categories)</em>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row"> <h3>Tags methods</h3></th>
				<td>
					<table>		
						<tr>
							<td>
								<label><input name="wponios_methods_options[gettags]" type="checkbox" value="1" <?php if (isset($options['gettags'])) { checked('1', $options['gettags']); } ?>/> All Tags </label>
							</td>
							<td>
								<em style="margin-left:32px">(URI template: GET /tags)</em>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="Save Changes" /> 
		</p>
	</div>
	<?php
}

function wponios_menu_render_about() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	?>
	
	<div class="wrap" style="text-align: center;margin: 10%; border: 1px solid #333; padding: 10px; -webkit-border-radius: 10px; -moz-border-radius: 10px;">
		<h2>You want/need an application of your blog on Smartphone ?</h2>
		<h2>Contact me direclty at <a href="mailto:vincentsaluzzo@me.com">vincentsaluzzo@me.com</a> or by my website: <a href="http://www.jesuisdeveloppeur.com">http://www.jesuisdeveloppeur.com</a></h2>
	</div>
	<?php
}

function eur_flush_rules() {    
  if (is_admin()) return;
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}       

function eur_redirect() {
	global $wp;
  	global $eur_urls;
  	$requestURI = explode('/', $wp->request);
  	
  	$options = get_option('wponios_options');
	$prefix = $options['custom_url_prefix'];
  	if($prefix == "") {
  		$prefix = "wponios";
  	}
  	$debug = $options['debug'];
  	if($debug == "1") {
  		$debug = true;
  	} else {
  		$debug = false;
  	}
  	
   	if($requestURI[0] === $prefix) {
		$app = new Slim(array("mode" => "production", "debug" => $debug));	
		// ************************ POST ******************************
		// ************************ POST ******************************
		// ************************ POST ******************************
		$app->get("/$prefix/posts", function() use ($app) {
			$app->response()->header('Content-type', 'application/json');
			$array = wponios_get_all_post_with_thumbnail();
			printf(json_encode($array));
		});
		
		$app->get("/$prefix/post/:postid/", function($postid) use ($app) {
			if(wponios_is_a_post($postid)) {
				$app->response()->header('Content-type', 'application/json');
				printf(json_encode(wponios_get_post($postid)));	
			} else {
				$app->notFound();
			}
		});
		
		$app->get("/$prefix/post/:postid/comments", function($postid) use ($app) {
			if(wponios_is_a_post($postid)) {
				$app->response()->header('Content-type', 'application/json');
				printf(json_encode(wponios_get_all_comment_of_post($postid)));
			} else {
				$app->notFound();
			}
		});
		
		$app->get("/$prefix/post/:postid/comment/:commentid", function($postid, $commentid) use ($app) {
			if(wponios_is_a_post($postid)) {
				$comment = wponios_get_comment($commentid);
				if($comment != null && $comment['comment_post_ID'] === $postid) {				
					$app->response()->header('Content-type', 'application/json');
					printf(json_encode($comment));
				} else {
					$app->notFound();
				}
			} else {
				$app->notFound();
			}
		});
		
		$app->put("/$prefix/post/:postid/comment", 
			function($postid) use ($app) {
			if(wponios_is_a_post($postid)) {
				$bodyJson = json_decode($app->request()->getBody());
				
				$author = $bodyJson->author;
				$email = $bodyJson->email;
				$comment = $bodyJson->comment;
				$url = $bodyJson->url;
				
				if($author == null || $email == null || $comment == null) {
					$app->error();
				}
				
				$result = wponios_post_comment($postid, $author, $email, $comment, $url);
				if($result === null) {
					$app->error();
				} else {
					echo $result;
				}
			} else {
				$app->notFound();
			}
		});
		
		
		
		
		// ************************ PAGE ******************************
		// ************************ PAGE ******************************
		// ************************ PAGE ******************************
		$app->get("/$prefix/pages", function() use ($app) {
			$app->response()->header('Content-type', 'application/json');
			$array = wponios_get_all_page();
			printf(json_encode($array));
		});
		
		$app->get("/$prefix/page/:pageid/", function($pageid) use ($app) {
			if(wponios_is_a_page($pageid)) {
				$app->response()->header('Content-type', 'application/json');
				printf(json_encode(wponios_get_page($pageid)));
				
			} else {
				$app->notFound();
			}
		});
		
		$app->get("/$prefix/page/:pageid/comments", function($pageid) use ($app) {
			if(wponios_is_a_page($pageid)) {
				$app->response()->header('Content-type', 'application/json');
				printf(json_encode(wponios_get_all_comment_of_page($pageid)));
			}
		});
		
		$app->get("/$prefix/page/:pageid/comment/:commentid", function($pageid, $commentid) use ($app) {
			if(wponios_is_a_page($pageid)) {
				$comment = wponios_get_comment($commentid);
				if($comment != null && $comment['comment_post_ID'] === $pageid) {
					$app->response()->header('Content-type', 'application/json');	
					printf(json_encode($comment));
				} else {
					$app->notFound();
				}	
			} else {
				$app->notFound();
			}
		});
		
		$app->put("/$prefix/page/:pageid/comment", 
			function($pageid) use ($app) {
			if(wponios_is_a_page($pageid)) {
				$bodyJson = json_decode($app->request()->getBody());
				
				$author = $bodyJson->author;
				$email = $bodyJson->email;
				$comment = $bodyJson->comment;
				$url = $bodyJson->url;
				
				if($author == null || $email == null || $comment == null) {
					$app->error();
				}
				
				$result = wponios_post_comment($pageid, $author, $email, $comment, $url);
				if($result === null) {
					$app->error();
				} else {
					echo $result;
				}
			} else {
				$app->notFound();
			}
		});
		
		
		
		
		$app->get("/$prefix/categories", function() use ($app) {
			$app->response()->header('Content-type', 'application/json');
			$array = wponios_get_all_categories();
			printf(json_encode($array));
		});
		
		$app->get("/$prefix/tags", function() use ($app) {
			$app->response()->header('Content-type', 'application/json');
			$array = wponios_get_all_tags();
			printf(json_encode($array));
		});
		
		$app->run();
	}
}
?>