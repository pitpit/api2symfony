# Api2Symfony

PHP library to automatically generate Symfony2 controllers from API specifications (RAML, Blueprint, Swagger...)

BUT... We only support the following specification formats now:

*  RAML

But we'd like to also support:

* Blueprint
* Swagger

> Feel free to submit your PRs !

Installation
------------

Using composer:

```sh
composer require "creads/api2symfony":"@dev"
```

Usage
-----

```php
//prepare RAML converter
$converter = new Creads\Api2Symfony\Converter\RamlConverter();

//prepare dumper
$dumper = new Creads\Api2Symfony\Dumper\SymfonyDumper();

//get controller models from specification
$controllers = $converter->generate('path/to/spec.raml');

//dump each controller into current directory
foreach($controllers as $controller) {
  $dumper->dump($controller);
}
```

Process Overview
----------------

Generate controllers [(see it)](http://www.nomnoml.com/#view/[<start>st]%20->[*.raml]%0A[*.raml]%20RamlConverter->[Mocks]%0A[Mocks]%20SymfonyDumper->[Definitions]%0A[Definitions]->[New%20Controllers]%0A[New%20Controllers]%20->[<end>e]):

```nomnoml
[<start>st] ->[*.raml]
[*.raml] RamlConverter->[Mocks]
[Mocks] SymfonyDumper->[Definitions]
[Definitions]->[New Controllers]
[New Controllers] ->[<end>e]
```

Update controllers [(see it)](http://www.nomnoml.com/#view/[<start>st]%20->[*.raml]%0A[*.raml]%20RamlConverter->[Mocks]%0A[Mocks]%20SymfonyDumper->[New%20Definitions]%0A[New%20Definitions]%20->[Diffs]%0A%0A[<start>st2]%20->[Old%20Controllers]%0A[Old%20Controllers]%20SymfonyLoader->[Old%20Definitions]%0A[Old%20Definitions]%20->%20[Diffs]%0A[Diffs]%20->[Merged%20Definitions]%0A[Merged%20Definitions]->[New%20Controllers]%0A[New%20Controllers]%20->[<end>e]):

```nomnoml
[<start>st] ->[*.raml]
[*.raml] RamlConverter->[Mocks]
[Mocks] SymfonyDumper->[New Definitions]
[New Definitions] ->[Diffs]

[<start>st2] ->[Old Controllers]
[Old Controllers] SymfonyLoader->[Old Definitions]
[Old Definitions] -> [Diffs]
[Diffs] ->[Merged Definitions]
[Merged Definitions]->[New Controllers]
[New Controllers] ->[<end>e]
```

Running tests
-------------

```sh
composer install
php vendor/bin/phpunit
```

## Contributors

* [Quentin Pautrat](https://github.com/qpautrat)
* [Damien Pitard](https://github.com/pitpit)

## Contributing

Feel free to contribute on github by submitting any issue or question on [tracker](https://github.com/creads/api2symfony/issues).
