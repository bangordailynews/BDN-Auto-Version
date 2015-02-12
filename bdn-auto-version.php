<?php
/*
Plugin Name: BDN Auto Version
Description: Checks the scripts as they're enqueued and uses last modified time as their version number
Version: 1.0
Author: Travis Weston
Contributors: anubisthejackle
Author URI: http://dev.bangordailynews.com/
*/

/* 
	Copied from wp-admin/includes/file.php
	Renamed due to those instances where people include file.php outside the admin area.
*/
function _get_home_path() {
        $home    = set_url_scheme( get_option( 'home' ), 'http' );
        $siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );
        if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
                $wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
                $pos = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
                $home_path = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
                $home_path = trailingslashit( $home_path );
        } else {
                $home_path = ABSPATH;
        }

        return str_replace( '\\', '/', $home_path );
}

if( !is_admin() && !function_exists('bdn_auto_version') ){

	function bdn_auto_version( $src ) {

		if( strpos( $src, 'wp-content' ) === false )
			return $src;
		
		$url = parse_url( $src );
		$home = untrailingslashit( _get_home_path() );

		if( !file_exists( $home . $url['path'] ) )
			return $src;

		return $url[ 'scheme' ] . '://' . $url['host'] . $url[ 'path' ] . '?ver=' . filemtime( $home . $url['path'] );

	}
	add_filter( 'script_loader_src', 'bdn_auto_version' );
	add_filter( 'style_loader_src', 'bdn_auto_version');

}
