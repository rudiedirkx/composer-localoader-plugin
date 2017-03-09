Localoader
===

Localoader adds a binary `locaload` with which you can choose to load dependencies
locally instead of using the Packagist version. This is especially useful when
developing your own packages, or others, in an existing project or context.

**N.B.** You still have to `composer require` the dependency!

## Use it

Alias an entire package:

	locaload alias author/some-package /var/www/dev/packages/author-some-package

Argument 1 is the Composer package name: `author/some-package`.

Argument 2 is the location of your checkout of that package: `/var/www/dev/packages/author-some-package`.

This will create and maintain a symlink from Composer's vendor dir to your dev checkout.

## Check it

To see which packages are 'localoaded':

    locaload list

Output will be something like:

	Currently loading:
	Array
	(
	    [alias] => Array
	        (
				[author/some-package] => /var/www/dev/packages/author-some-package
			)
	)

### Deprecation warning

This package used to load namespaces locally, without symlink, but that didn't include all of
Composer's `"autoload"` properties. Your `composer-locaload.json` might have `psr-0` or `psr-4`
config. Remove that, and replace it with `alias`.
