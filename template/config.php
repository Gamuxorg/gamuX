<?php
//from https://wordpress.stackexchange.com/questions/217027/undefined-constant-with-debug-set-to-true
function gamux_single_template($template) {
    // Get the current single post object
    $post = get_queried_object();
    // Set our 'constant' folder path
    $path = 'template/';
    $single_slug_head = 'single-slugs-';

    // Set our variable to hold our templates
    $templates = [];

    // Lets handle the custom post type section
    if ( 'post' != $post->post_type ) {
        $templates[] = $path . 'single-' . $post->post_type . '-' . $post->post_name . '.php';
        $templates[] = $path . 'single-' . $post->post_type . '.php';
    }

    // Lets handle the post post type stuff
    if ( 'post' == $post->post_type ) {
        // Get the post categories
        $categories = get_the_category( $post->ID );
        // Just for incase, check if we have categories
        if ( $categories ) {
            foreach ( $categories as $category ) {
                while($category->category_parent) {
                    $category = get_category($category->category_parent);
                }
                // Create possible template names
                $templates[] = $path . $single_slug_head . $category->slug . '.php';
//                $templates[] = $path . $single_slug_head . $category->term_id . '.php';
            } //endforeach
        } //endif $categories
    } // endif  

    // Set our fallback templates
    $templates[] = $path . 'single.php';
    $templates[] = $path . 'term.php';

    /**
     * Now we can search for our templates and load the first one we find
     * We will use the array ability of locate_template here
     */
    $template = locate_template( $templates );

    // Return the template rteurned by locate_template
    return $template;
}
add_filter( 'single_template', 'gamux_single_template');