<?php

/* Code to Display Featured Image on top of the post */
/* https://wpism.com/display-featured-image-in-genesis-themes/ */
add_action( 'genesis_entry_content', 'ghh_featured_post_image', 8 );
function ghh_featured_post_image() {
  if ( ! is_singular( 'post' ) )  return;
    the_post_thumbnail('post-image');
}

/* Wireframe does not include author and date */
/* https://my.studiopress.com/documentation/snippets/post-meta-xhtml/remove-the-post-meta-function/ */
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );

// add mailing list form via beaver builder shortcode
// [fl_builder_insert_layout slug="test-text-and-form-group"]
add_action( 'genesis_before_comment_form', 'ghh_add_bb_subscribe_form' );
function ghh_add_bb_subscribe_form() {
    echo do_shortcode('[fl_builder_insert_layout slug="subscribe-form-active-camp-inline"]');
}

genesis();
