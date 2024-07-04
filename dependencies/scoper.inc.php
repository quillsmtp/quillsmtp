<?php
declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

return [
    // The prefix configuration. If a non null value will be used, a random prefix will be generated.
    'prefix' => 'QuillSMTP\Vendor',

    // By default when running php-scoper add-prefix, it will prefix all relevant code found in the current working
    // directory. You can however define which files should be scoped by defining a collection of Finders in the
    // following configuration key.
    //
    // For more see: https://github.com/humbug/php-scoper#finders-and-paths
    'finders' => [
        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->notName('/.*\\.md|.*\\.dist|Makefile|composer\\.json|composer\\.lock/')
            ->exclude([
                'doc',
                'test',
                'test_old',
                'tests',
                'Tests',
                'vendor-bin',
            ])
            ->in('vendor'),
        Finder::create()->append([
            'composer.json',
            'composer.lock',
        ]),
    ],

    // When scoping PHP files, there will be scenarios where some of the code being scoped indirectly references the
    // original namespace. These will include, for example, strings or string manipulations. PHP-Scoper has limited
    // support for prefixing such strings. To circumvent that, you can define patchers to manipulate the file to your
    // heart contents.
    //
    // For more see: https://github.com/humbug/php-scoper#patchers
    'patchers' => [
        function (string $filePath, string $prefix, string $contents): string {
            // replace '\\Brevo\\Client\\Model\\' with \\QuillSMTP\\Vendor\\Brevo\\Client\\Model\\'
            $contents = str_replace(
                '\'\\\\Brevo\\\\Client\\\\Model\\\\',
                '\'\\\\QuillSMTP\\\\Vendor\\\\Brevo\\\\Client\\\\Model\\\\',
                $contents
            );

            // replace '\\SendGrid\\Mail\\' with \\QuillSMTP\\Vendor\\SendGrid\\Mail\\'
            $contents = str_replace(
                '\'\\\\SendGrid\\\\Mail\\\\',
                '\'\\\\QuillSMTP\\\\Vendor\\\\SendGrid\\\\Mail\\\\',
                $contents
            );

            return $contents;
        },

        /**
		 * Prefix the dynamic alias class generation in Google's apiclient lib.
		 *
		 * @param string $filePath The path of the current file.
		 * @param string $prefix   The prefix to be used.
		 * @param string $content  The content of the specific file.
		 *
		 * @return string The modified content.
		 */
		function ( $file_path, $prefix, $content ) {
			if ( strpos( $file_path, 'google/apiclient/src/aliases.php' ) !== false ) {
				return str_replace(
					'class_alias($class, $alias);',
					sprintf( 'class_alias($class, \'%s\\\\\' . $alias);', addslashes( $prefix ) ),
					$content
				);
			}
			return $content;
		},

		/**
		 * Prefix the Guzzle client interface version checks in Google HTTP Handler Factory and
		 * Google Credentials Loader.
		 *
		 * @param string $filePath The path of the current file.
		 * @param string $prefix   The prefix to be used.
		 * @param string $content  The content of the specific file.
		 *
		 * @return string The modified content.
		 */
		function ( $file_path, $prefix, $content ) {
			if (
				strpos( $file_path, 'google' ) !== false
			) {
				return str_replace(
					'GuzzleHttp\\\\ClientInterface',
					sprintf( '%s\\\\GuzzleHttp\\\\ClientInterface', addslashes( $prefix ) ),
					$content
				);
			}
			return $content;
		},
    ],
];
