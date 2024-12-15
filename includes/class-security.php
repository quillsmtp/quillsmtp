<?php
/**
 * Security class
 *
 * @package QuillSMTP
 * @since 1.0.0
 */

namespace QuillSMTP;

/**
 * Security static class
 */
class Security {

	/**
	 * Create upload dir recursive and adding security files to it
	 *
	 * @param string $dir Upload dir.
	 * @return boolean
	 */
	public static function prepare_upload_dir( $dir ) {
		// no need for checks.
		if ( is_dir( $dir ) ) {
			return true;
		}

		// recursive dir creation.
		if ( ! wp_mkdir_p( $dir ) ) {
			return false;
		}

		// adding index files is a must!, this can fail only if path isn't writable.
		if ( ! self::add_index_files_recursive( $dir ) ) {
			return false;
		}

		// add htaccess file.
		self::add_htaccess_file();

		return true;
	}

	/**
	 * Recursive index files creation from root upload dir to this dir.
	 *
	 * @param string $dir Full dir path.
	 * @return boolean
	 */
	private static function add_index_files_recursive( $dir ) {
		$root_dir = QuillSMTP::get_upload_dir();
		if ( ! self::add_index_files( $root_dir ) ) {
			return false;
		}

		$relative_dir      = trim( str_replace( $root_dir, '', $dir ), '/' );
		$relative_sub_dirs = explode( '/', $relative_dir );

		$current_dir = $root_dir;
		foreach ( $relative_sub_dirs as $sub_dir ) {
			$current_dir .= "/$sub_dir";
			if ( ! self::add_index_files( $current_dir ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Add index.php and index.html to a dir
	 *
	 * @param string $dir Full dir path.
	 * @return boolean
	 */
	private static function add_index_files( $dir ) {
		$index_php  = file_exists( "$dir/index.php" ) || file_put_contents( "$dir/index.php", "<?php // Don't remove this file." );
		$index_html = file_exists( "$dir/index.html" ) || file_put_contents( "$dir/index.html", "<!-- Don't remove this file. -->" );
		return $index_php || $index_html;
	}

	/**
	 * Add .htaccess file to uploads root dir
	 *
	 * @return boolean
	 */
	private static function add_htaccess_file() {
		$htaccess = QuillSMTP::get_upload_dir() . '/.htaccess';
		if ( file_exists( $htaccess ) ) {
			return true;
		}

		// multiple rules as maybe some directives aren't allowed.
		$rules = '# Don\'t remove this.
Deny from all
Options -Indexes
<IfModule mod_php5.c>
  php_flag engine off
</IfModule>
<IfModule headers_module>
  Header set X-Robots-Tag "noindex"
</IfModule>
<Files *>
  SetHandler none
  SetHandler default-handler
  Options -ExecCGI
  RemoveHandler .cgi .php .php3 .php4 .php5 .phtml .pl .py .pyc .pyo
</Files>';
		$rules = explode( "\n", $rules );

		if ( ! function_exists( 'insert_with_markers' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/misc.php' );
		}
		return insert_with_markers( $htaccess, 'QuillSMTP', $rules );
	}

}
