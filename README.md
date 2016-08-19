# Mailer

(---- IT JUST MAKES BULK MAILING EASY ----)

<!-- ![](cover.png) -->

<p align="center">
  <a href="LICENSE">
    <img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License" />
  </a>
  <a href="https://packagist.org/packages/scalex/mailer">
    <img src="https://img.shields.io/packagist/v/scalex/mailer.svg?style=flat-square" alt="Packagist" />
  </a>
  <a href="https://github.com/scalexsystems/mailer/releases">
    <img src="https://img.shields.io/github/release/scalexsystems/mailer.svg?style=flat-square" alt="Latest Version" />
  </a>

  <a href="https://github.com/scalexsystems/mailer/issues">
    <img src="https://img.shields.io/github/issues/scalexsystems/mailer.svg?style=flat-square" alt="Issues" />
  </a>
</p>

## Install

Via Composer

``` bash
$ composer require scalex/mailer
```

## Usage

``` bash
# Initialize mailer.
$ mailer init 
# Create new project.
$ mailer new product-campaign
$ cd product-campaign
# Update html.blade.php, text.blade.php, config.php and 
# data.csv with relevent information.
# Then :)
$ mailer send
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email opensource@scalex.xyz instead of using the issue tracker.

## Credits

- [:author_name][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-author]: https://github.com/scalexsystems
[link-contributors]: ../../contributors
