Localoader
===

Localoader adds a binary `locaload` with which you can choose to load dependencies
locally instead of using the Packagist version. This is especially useful when
developing your own packages, or others, in an existing project or context.

You still have to `composer require` the dependency, and it still exists on disk,
but that version won't be loaded.

Use it
---

	# Default is PSR-4
	locaload someone\\somepackage /var/www/dev/packages/somepackage
	composer dump-autoload

	# Change PSR to 0
	locaload 0 someone\\somepackage /var/www/dev/packages/somepackage
	composer dump-autoload

This will add a file `composer-locaload.json` in the project's root, and override
the project's `vendor/autoload.php`.

**N.B.** Don't forget to escape backslashes! Bash will remove a single `\`, so be sure
to make it a `\\`, or use a `/` in the namespace. `locaload` will understand:

	# Forward slashed namespaces are supported
	locaload someone/somepackage /var/www/dev/packages/somepackage

**N.B.** This looks like you're referencing the Packagist package name, but you're
not! You're referencing the **PHP namespace** in the package's `composer.json`.

Check it
---

To see which packages are 'localoaded':

    locaload

Output will be something like:

	Currently loading:
	Array
	(
	    [psr-4] => Array
	        (
				[rdx\laraveleagerrelationships] => /var/www/tests/composer/laravel-eager-relationships
			)
	)
