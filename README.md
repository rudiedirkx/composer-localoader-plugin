Localoader
===

Localoader adds a binary `locaload` with which you can choose to load dependencies
locally instead of using the Packagist version. This is especially useful when
developing your own packages, or others, in an existing project or context.

You still have to `composer require` the dependency, and it still exists on disk,
but that version won't be loaded.

Use it
---

	locaload someone\\somepackage /var/www/dev/packages/somepackage
	composer dump-autoload

This will add a file `composer-locaload.json` in the project's root, and override
the project's `vendor/autoload.php`.

Check it
---

To see which packages are 'localoaded':

    locaload

Output will be something like:

	Currently loading:
	Array
	(
		[rdx\laraveleagerrelationships] => /var/www/tests/composer/laravel-eager-relationships
	)

