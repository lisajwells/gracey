<?php

/* Code to Display Featured Image on top of the post */
/* https://wpism.com/display-featured-image-in-genesis-themes/ */
add_action( 'genesis_entry_content', 'ghh_featured_post_image', 8 );
function ghh_featured_post_image() {
  if ( ! is_singular( 'post' ) )  return;
    the_post_thumbnail('post-image');
}






genesis();