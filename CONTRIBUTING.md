Thanks for considering contributing to this project! Pull requests are welcome,
and if you want to discuss something before starting (or just check if the maintainer is still alive),
feel free to open an issue first.

## Code style

This project comes with a [ruleset.xml](https://github.com/scriptotek/php-marc/blob/master/ruleset.xml) file that
defines the code style (which is PSR-2 at the time of this writing), so it can be checked with
[phpcs](https://github.com/squizlabs/PHP_CodeSniffer):

    phpcs --standard=ruleset.xml src

The project also comes with an [.overcommit.yml](https://github.com/scriptotek/php-marc/blob/master/.overcommit.yml)
file so you can use [Overcommit](https://github.com/sds/overcommit)'s Git hooks to have your changes checked before
each commit.

## Tests

Tests are run using [PhpUnit](https://phpunit.de/):

    ./vendor/bin/phpunit

If you add new functionality, please consider also adding a test case for it.

## Changelog

Consider adding an entry to the [CHANGELOG](https://github.com/scriptotek/php-marc/blob/master/CHANGELOG.md) as part
of your commit.

## Code of conduct

This project adheres to the [Open Code of Conduct][code-of-conduct]. By
participating, you are expected to honor this code.

[code-of-conduct]: https://github.com/civiccc/code-of-conduct
