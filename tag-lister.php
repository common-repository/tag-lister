<?php /*

**************************************************************************

Plugin Name:  Tag Lister
Plugin URI:   http://blog.wandr.me/plugins/Tag-Lister/
Description:  Easily embed a trip report index (or other list of commonly tagged posts) into another post
Version:      0.0.1
Author:       Seth Miller
Author URI:   http://blog.wandr.me/

**************************************************************************

Copyright (C) 2017 Proton Associates, LLC

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

**************************************************************************

In short, this plugin is free to use by anyone and everyone. You are
welcome to use it on a commercial site or whatever you want. However, I do
very much appreciate donations for all of my time and effort, although
they obviously aren't required for you to use this plugin. You can even
just buy me a beer.

If you sell this code (i.e. are a web developer selling features provided
via this plugin to clients), it would be very nice if you threw some of
your profits my way. After all, you are profiting off my hard work. ;)

Thanks and enjoy this plugin!

**************************************************************************/




	// Handle taglist shortcodes
	function shortcode_taglist( $atts, $content = '' ) {
		// Set any missing $atts items to the defaults
		$atts = shortcode_atts(array(
			'trtag'	=> '',
			'trstyle'     => 'ul',
			'trheader'	=> 'More from this trip:',
			'trheadstyle'	=> 'h2'
			), $atts);


		//If there's nothing in the path then this is all a waste of time
		if ($atts[trtag]=='') {
		return 'Error generating trip report; please alert the site admin.';
		}

		//get the posts
		$trTag=$atts[trtag];
		$args = array('tag_slug__and' => array($trTag),'posts_per_page'=> -1,'orderby'=> 'post_date','order'=> 'ASC',);
		$postslist = get_posts( $args );


		//Build it
		switch ($atts[trstyle]) {
			case 'ol':
			case 'ul':
				$trCodeStart='<' . $atts[trstyle] . '>';
				$trCodeEnd='</' . $atts[trstyle] . '>';
				$trItemEntry='<li>';
				$trItemEnd='</li>';
				break;
			case 'h1':
			case 'h2':
			case 'h3':
			case 'h4':
			case 'h5':
			case 'h6':
			case 'p':
				$trItemEntry='<' . $atts[trstyle] . '>';
				$trItemEnd='</' . $atts[trstyle] . '>';
				break;
			default:
				$trItemEntry='<h4>';
				$trItemEnd='</h4>';
			}
			$trCodeMiddle='';
			foreach ($postslist as $post) {
				setup_postdata($post);
				$trCodeMiddle .= $trItemEntry . '<a href="' . get_permalink($post) . '">' . get_the_title($post) . '</a>' . $trItemEnd;

			}
			wp_reset_postdata();

		return do_shortcode('<' . $atts[trheadstyle] . '>' . $atts[trheader] . '</' . $atts[trheadstyle] . '>' . $trCodeStart . $trCodeMiddle . $trCodeEnd);

	}



	// Handle tagprevnext shortcode
	function shortcode_tagprevnext( $atts, $content = '' ) {
		// Set any missing $atts items to the defaults
				$atts = shortcode_atts(array(
					'trtag'	=> '',
					'trstyle'     => 'h4',
					'trusetitle' => '1'
					), $atts);



		//If there's nothing in the path then this is all a waste of time
				if ($atts[trtag]=='') {
				return 'Error generating trip report links; please alert the site admin.';
				}

				//get the posts
				$trTag=$atts[trtag];
				$args = array('tag_slug__and' => array($trTag),'posts_per_page'=> -1,'orderby'=> 'post_date','order'=> 'ASC',);
				$postslist = get_posts( $args );

				$posts = array();
				foreach ( $postslist as $post ) {
				   $posts[] += $post->ID;
				}

				$current = array_search( get_the_ID(), $posts );
				$prevID = $posts[$current-1];
				$nextID = $posts[$current+1];
				$post_id = get_the_ID();

				//Build it
				switch ($atts[trstyle]) {
					case 'h1':
					case 'h2':
					case 'h3':
					case 'h4':
					case 'h5':
					case 'h6':
					case 'p':
						$trItemEntry='<' . $atts[trstyle] . '>';
						$trItemEnd='</' . $atts[trstyle] . '>';
						break;
					default:
						$trItemEntry='<h4>';
						$trItemEnd='</h4>';
					}

					$trCodeMiddle='';

					if (in_array($atts[trusetitle],array('false', 'False', 'FALSE', 'no', 'No', 'n', 'N', '0', 'off', 'Off', 'OFF', false, 0, null), true)) {
						if (is_null($prevID)==1) {
							$trCodeMiddle = $trItemEntry . '| <a href="' . get_permalink($nextID) . '">Next >></a>' . $trItemEnd ;
						}
						elseif (is_null($nextID)==1) {
							$trCodeMiddle = $trItemEntry . '<a href="' . get_permalink($prevID) . '"><< Previous</a> | ' . $trItemEnd ;
						}
						else {
							$trCodeMiddle = $trItemEntry . '<a href="' . get_permalink($prevID) . '"><< Previous</a> | <a href="' . get_permalink($nextID) . '">Next >></a>' . $trItemEnd ;
						}

					}
					else
					{
						if (is_null($prevID)==1) {
							$trCodeMiddle = $trItemEntry . '| <a href="' . get_permalink($nextID) . '">' . get_the_title($nextID) . ' >></a>' . $trItemEnd ;
						}
						elseif (is_null($nextID)==1) {
							$trCodeMiddle = $trItemEntry . '<a href="' . get_permalink($prevID) . '"><< ' . get_the_title($prevID) . '</a> |' . $trItemEnd ;
						}
						else {
							$trCodeMiddle = $trItemEntry . '<a href="' . get_permalink($prevID) . '"><< ' . get_the_title($prevID) . '</a> | <a href="' . get_permalink($nextID) . '">' . get_the_title($nextID) . ' >></a>' . $trItemEnd ;
						}
					}



		return do_shortcode($trCodeMiddle );

	}



		// Register shortcodes
		add_shortcode( 'taglist', 'shortcode_taglist' );
		add_shortcode( 'tagprevnext', 'shortcode_tagprevnext' );
?>