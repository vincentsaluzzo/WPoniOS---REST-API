<?php
/*

Copyright 2012 Vincent Saluzzo

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

// POST
function wponios_get_post_status($post_id) {
	return get_post_status($post_id);
}

function wponios_is_a_post($post_id) {
	$post = wponios_get_post($post_id);
	$result = false;
	if($post != null && $post['post_type'] === 'post') {
		$result = true;
	} 
	return $result;
}
function wponios_get_post($post_id) {
	$post = get_post($post_id, ARRAY_A);
	return $post;
}

function wponios_get_post_with_thumbnail($post_id) {
	$arrayOfPost = wponios_get_post($post_id);
	$thumbnail = get_the_post_thumbnail($post_id, 'full');
	if($thumbnail != null) {
		$arrayOfPost['thumbnailEncoded'] = $thumbnail;
		preg_match_all('/(src)=\\"([^"]*)\\"/i', $arrayOfPost['thumbnailEncoded'] , $img);
		$arrayOfPost['thumbnail'] = $img[2][0];
	} else {
		$arrayOfPost['thumbnailEncoded'] = "";
		$arrayOfPost['thumbnail'] = "";
	}
	return $arrayOfPost;
}

function wponios_get_all_post_id() {
	$args = array('numberposts' => -1, 'orderby' => 'post_date', 'order' => 'DESC');
	$listeOfPostsID = array();
	foreach (get_posts($args) as $post) {
		$listeOfPostsID[] = $post->ID;
	}
	
	return $listeOfPostsID;
}


function wponios_get_all_post() {
	$postsID = wponios_get_all_post_id();
	$listeOfPosts = array();
	foreach ($postsID as $post_id) {
		$listeOfPosts[] = wponios_get_post($post_id);
	}
	return $listeOfPosts;
}

function wponios_get_all_post_with_thumbnail() {
	$postsID = wponios_get_all_post_id();
	$listeOfPosts = array();
	foreach ($postsID as $post_id) {
		$listeOfPosts[] = wponios_get_post_with_thumbnail($post_id);
	}
	return $listeOfPosts;
}




// CATEGORY
function wponios_get_category($category_id) {
	$category = get_category($category_id, ARRAY_A);
	return $category;
}

function wponios_get_all_categories() {
	$args=array('orderby' => 'name', 'order' => 'ASC');
	$categories = array();
  	foreach(get_categories($args) as $category) { 
  		$categories[] = wponios_get_category($category->cat_ID);
    } 
    return $categories;
}


// TAGS
function wponios_get_tag($tag_id) {
	$tag = get_tag($tag_id, ARRAY_A);
	return $tag;
}

function wponios_get_all_tags() {
	$tags = get_tags();
	$arrayOfTags = array(); 
	foreach ($tags as $tag) {
		$arrayOfTags[] = wponios_get_tag($tag);
	}	
	return $arrayOfTags;
}

// PAGES
function wponios_is_a_page($post_id) {
	$post = wponios_get_post($post_id);
	$result = false;
	if($post != null && $post['post_type'] === 'page') {
		$result = true;
	} 
	return $result;
}

function wponios_get_page($page_id) {
	$page = array();
	$page = get_page($page_id, ARRAY_A);
	return $page;
}

function wponios_get_all_page_id() {
	$pagesID = get_all_page_ids();
	return $pagesID;
}

function wponios_get_all_page() {
	$arrayOfPage = array();
	foreach (wponios_get_all_page_id() as $page_id) {
		$arrayOfPage[] = wponios_get_page($page_id);
	}
	return $arrayOfPage;
}

// COMMENTS
function wponios_get_comment($comment_id) {
	return get_comment($comment_id, ARRAY_A);
}

function wponios_get_all_comment_of_post($post_id) {
	$comments = get_approved_comments($post_id);
	$arrayOfComments = array();
	foreach ($comments as $comment) {
		$arrayOfComments[] = wponios_get_comment($comment->comment_ID);
	}
	return $arrayOfComments;
}

function wponios_post_comment($post_id, $author, $email, $comment, $url = "") {
	if(isset($post_id) && isset($author) && isset($email) && isset($comment) && isset($url)) {
		$comment_data = array(
				'comment_post_ID' => $post_id,
				'comment_author' => $author,
				'comment_author_email' => $email,
				'comment_author_url' => $url,
				'comment_content' => $comment,
				'comment_type' => ''
		);
		
		return wp_new_comment($comment_data);
	} else {
		return null;
	}
}
?>