<?php
/**
 * Based on TwentyTen functions and definitions
 **/
 
//include 'plugins/drop-caps/wp_drop_caps.php';
// if (!function_exists('arl_kottke_archives')) {
// include 'plugins/arl_kottke_archives.php';
// }
/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640;

/** Tell WordPress to run twentyten_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'twentyten_setup' );

if ( ! function_exists( 'twentyten_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override twentyten_setup() in a child theme, add your own twentyten_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_setup() {

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'twentyten', TEMPLATEPATH . '/languages' );

	// This theme uses wp_nav_menu() in one locations.
	register_nav_menus( array(
		'primary' => __( 'Footer Navigation Menu', 'twentyten' )
	) );
}
endif;

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'twentyten_page_menu_args' );

/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since Twenty Ten 1.0
 * @return int
 */// Changing excerpt length
function new_excerpt_length($length) {
return 40;
}
add_filter('excerpt_length', 'new_excerpt_length');

// Changing excerpt more
function new_excerpt_more($more) {
return '&hellip; ' . ' <a href="'. get_permalink() . '">' . __( 'weiterlesen', 'twentyten' ) . '</a>' . ' &raquo;';
}
add_filter('excerpt_more', 'new_excerpt_more');

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function twentyten_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= new_excerpt_more(40);
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'twentyten_custom_excerpt_more' );

if ( ! function_exists( 'twentyten_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post--date/time and author.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_posted_on() {
	printf( __( '%2$s', 'twentyten' ),
		'meta-prep meta-prep-author',
		sprintf( 'Ver&ouml;ffentlicht am %3$s',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		)
	);
}
endif;

/*
*	Changed on 2014 04
*	Warning if url (set in backend menu-konfiguration) of link is empty 
* 	so: 
*	$help = $item->url;
*	if (empty($help))
*	{
*		$help = '#';
*	}
*	normally it should be done by a new walker function
*	
**/
add_filter('nav_menu_css_class', 'AddCurrentMenuItemClass',1,2);

function AddCurrentMenuItemClass($classes,$item)
{
	$link = site_url().$_SERVER['REQUEST_URI'];
	$help = $item->url;
	if (empty($help))
	{
		$help = '#';
	}
	if(strpos($link, $help) !== false)
	{
		$classes[] = 'current-menu-item';
	}
	return $classes;
}

if ( ! function_exists( 'twentyten_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since Twenty Ten 1.0
 * customized because of categorie slug 20140509
 * added <a href="http://johnsplace.com" rel="author">John</a> to author
 */
function twentyten_posted_in() {
	/* translators: used between list items, there is a space after the comma */
	$categories_list = get_the_category_list( __( ', ', 'twentyeleven' ) );

	/* translators: used between list items, there is a space after the comma */
	$tag_list = get_the_tag_list( '', __( ', ', 'twentyeleven' ) );
	if ( '' != $tag_list ) {
		$utility_text = __( 'Wurde gepostet in %1$s. Schl&uuml;sselw&ouml;rter %2$s. Autor: <a class="p-author" href="%6$s" rel="author">%5$s</a>.', 'twentyeleven' );
	} elseif ( '' != $categories_list ) {
		$utility_text = __( 'Wurde gepostet in %1$s. Autor: <a class="p-author" href="%6$s" rel="author">%5$s</a>.', 'twentyeleven' );
	} else {
		$utility_text = __( 'Autor: <a class="p-author" href="%6$s" rel="author">%5$s</a>.', 'twentyeleven' );
	}
	$categories = str_replace("/category", '', $categories_list);
	//echo the_author_posts_link(); Testporpos
	printf(
		$utility_text,
		$categories,
		$tag_list,
		esc_url($permalink),
		the_title_attribute( 'echo=0' ),
		get_the_author(),
		home_url()
	);
}
endif;

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function theme_name_wp_title( $title, $sep ) {
	
	global $post; //the whole post object
	
	//Add title of the book
	if($post->post_parent == 644)
	{
		$title .= " 20er Jahre $sep ";
	}elseif($post->post_parent == 626)
	{
		$title .= " Borso Bibel $sep ";
	}
	
	
	// Add the blog name
	$title .= get_bloginfo( 'name', 'display' );
	
	// Add the title for the home/front page.
	if (is_front_page()) {
		$title .= " $sep ". get_the_title($post->ID);
	}

	return $title;
}
add_filter( 'wp_title', 'theme_name_wp_title', 10, 2 );

remove_action( 'wp_head',             'feed_links',                      2     );
remove_action( 'wp_head',             'feed_links_extra',                3     );
remove_action( 'wp_head',             'rsd_link'                               );
remove_action( 'wp_head',             'wlwmanifest_link'                       );
remove_action( 'wp_head', 'wp_generator');
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 ); // Remove WordPress shortlink on wp_head hook


/* navigation for books
*	Changed on 20140528
*		- devider on last link
*		- 2d array
*		- using of foreach instead of isset
*		
*	Possible improvements:
*		create array dynamically
*		add devider character as parameter
*		add template shortcode, so we are not forced to use different page/posttypes
*
*/
function nav_book_onpost($postid, $show) {
	unset($navlinks, $post_navi);
	if($show)
	{
		//create array
		
		$navlinks = array( 'prev' => array('permalink' => '',
											'title'	=>	'',
											'text'	=> 'Zur&uuml;ck',
											'type' => 'prev')
							,
							'content'=> array('permalink' => '',
											'title'	=>	'',
											'text'	=> 'Inhaltsverzeichnis',
											'type' => 'first')
							,
							'next' => array('permalink' => '',
											'title'	=>	'',
											'text'	=> 'Weiter',
											'type' => 'next')
							);
		
		// get permalink and title of post
		foreach($navlinks as $key => $value)
		{
			$id = get_post_meta($postid, $key, true);
			if($id != '')
			{
				$navlinks[$key]['permalink'] = get_permalink($id); 
				$navlinks[$key]['title'] = get_the_title($id);
			}
			else
			{	//delete empty elements in array
				unset($navlinks[$key]);
			}
		}
		//var_dump($navlinks);
		
		//parse array and do list with a and devider
		$post_navi = '<nav class="nav_post">';
		foreach($navlinks as $key => $value)
		{
			if($navlinks[$key]['permalink'] != '')
			{
				$post_navi .= '<a rel="'.$navlinks[$key]['type'].'" href="'.$navlinks[$key]['permalink'].'" title="'.$navlinks[$key]['title'].'">'.$navlinks[$key]['text'].'</a>'; 	
				if(end($navlinks) !== $value)
				{
					// not the last element
					$post_navi .= ' | ';
				}
			}
		}
		$post_navi .= '</nav>';
		echo $post_navi;
		
	}
	else 
	{
		return;
	}
}

/* function metagenerator
*  will put some meta tags for <head>
*  function is hooked automatically in wp_head
*	
* 	Improvements:
*		- add title and other stuff
*		- there is a plugin which combines meta and link with opg
*	
*/
function meta_gen()
{
	$meta_array = array(
						'keywords' => '',
						'date' => '',
						'author' => '',
						'description' => '',
						'google-site-verification'=> ''
						);//array alla name and content
	$meta_array["keywords"] = "Borso Bibel, die 20er Jahre, Autographen , Sammler, historische Studien";
	$meta_array["date"] = get_date();
	$meta_array["description"] = get_desc();
	$meta_array["author"] = "Wolfgang Reitzi";
	$meta_array["google-site-verification"] = 'fVYSNldzTczoT5BMLB64qhHKJt10KBec0paTxhl5IXg';
	foreach($meta_array as $name => $content)
	{
		if($content!='')
		{
			echo '<meta name="'.$name.'" content="'.$content.'">'; 
		}
	}
	
	unset($meta_array);
}
add_action('wp_head', 'meta_gen');

function get_date(){
	Global $post;
	if(is_single() && !($post->post_parent == 644) && !($post->post_parent == 626))
	{
		return get_the_time(c);
	}else
	{
		return "";
	}
}
/*to change for books so that Bookname - Title: postmeta */
function get_desc(){
	$description = '';
	Global $post;
	
	if(is_page() && ($post->post_parent == 626 || $post->post_parent == 644))
	{
		if($post->post_parent == 626)
		{
			$description = 'Borso Bibel - ';
		}
		if($post->post_parent == 644)
		{
			$description = '20er Jahre - ';
		}
		$description .= get_post_meta( get_the_ID(), 'meta_description', true );
		$description .= ': ';
		$description .= get_the_title();
		
		return $description;
		
	}elseif(is_single())
	{
		$description = get_the_title();
		$description .= ': ';
		$description .= get_post_meta( get_the_ID(), 'meta_description', true );
		return $description;
		
	}elseif(is_category())
	{//if is category take categorydescription
		// not sure if there is a better method as single_cat_title
		$description = single_cat_title('', false); 
		$description .= ': '; 
		$category =  get_term_by( 'name', single_cat_title('', false), 'category');
		$description .= esc_attr($category->description);
		return $description;
	}elseif(is_page() && !is_front_page())
	{//if is page take meta_description from customfields
		$description =get_the_title(); 
		$description .= ': '; 
		$description .= get_post_meta(get_the_ID(), 'meta_description', true);
		return $description;
	}else
	{//for everypage else take blogdescription and title but at the moment blogdescription has to be fixed 
	 // cause of the custom conditions
		$description =get_the_title(); 
		$description .= ': '; 
		$description .= get_bloginfo('description');
		return $description;
	}
}

function head_description(){
	$description = '';
	if(is_single() || $post->post_parent == 626 || $post->post_parent == 644)
	{
		$description = "";
		echo $description;
	}elseif(is_category())
	{//if is category take categorydescription
		// not sure if there is a better method as single_cat_title
		$category =  get_term_by( 'name', single_cat_title('', false), 'category');
		$description .= esc_attr($category->description);
		echo '<div id="site-description"><p>'.$description.'</p></div>';
	}
	else 
	{//for instance do nothing
		return;
	}

}

/* searchform*/
function my_search_form( $form ) {
    $form = '<form role="search" method="get" class="search-form" action="' . esc_url( home_url( '/' ) ) . '">
				<label><span class="screen-reader-text"></span>
					<input type="search" class="search-field" placeholder="' . "Suchbegriff" . '" value="' . get_search_query() . '" name="s" title="' ."Suche nach dem Begriff" . '" />
				</label>
				<input type="submit" class="search-submit" value="'. "Suchen" .'" />
			</form>';

    return $form;
}
add_filter( 'get_search_form', 'my_search_form' );

/* function to create sitemap.xml file in root directory of site  

add_action("publish_post", "eg_create_sitemap");
add_action("publish_page", "eg_create_sitemap");

add_action("save_post", "eg_create_sitemap");

function eg_create_sitemap() {
  $postsForSitemap = get_posts(array(
    'numberposts' => -1,
    'orderby' => 'modified',
    'post_type'  => array('post','page'),
    'order'    => 'DESC'
  ));



  $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';

$sitemap .= "\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";


  foreach($postsForSitemap as $post) {
    setup_postdata($post);

$postdate = explode(" ", $post->post_modified);

$sitemap .= "\t".'<url>'."\n".
  "\t\t".'<loc>'. get_permalink($post->ID) .'</loc>'.
  "\n\t\t".'<lastmod>'. $postdate[0] .'</lastmod>'.
  "\n\t\t".'<changefreq>monthly</changefreq>'.
"\n\t".'</url>'."\n";
  }

  $sitemap .= '</urlset>';

  $fp = fopen(ABSPATH . "sitemap.xml", 'w');
  fwrite($fp, $sitemap);
  fclose($fp);
}*/

/** Function new custom excerpt
*
*	is an override of the wp_trim_excerpt function
*	hooked to the excerpt function of core
*
**/

function improved_trim_excerpt($text)
{ // Fakes an excerpt if needed
  global $post;
  if ( '' == $text ) {
    $text = get_the_content('');
    $text = apply_filters('the_content', $text);
    $text = str_replace('\]\]\>', ']]&gt;', $text);
    $text = strip_tags($text, '<p>,<blockquote>');
    $excerpt_length = 30;
    $words = explode(' ', $text, $excerpt_length + 1);
    if (count($words)> $excerpt_length) {
      array_pop($words);
      array_push($words, new_excerpt_more($excerpt_lenght));
      $text = implode(' ', $words);
    }
  }
  return $text;
}

remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'improved_trim_excerpt');

?>




