# Rector - Upgrade your Legacy App to Modern Codebase

[![Build Status](https://img.shields.io/travis/rectorphp/rector/master.svg?style=flat-square)](https://travis-ci.org/rectorphp/rector)
[![Coverage Status](https://img.shields.io/coveralls/RectorPHP/Rector/master.svg?style=flat-square)](https://coveralls.io/github/rectorphp/rector?branch=master)


Rector **upgrades your application** for you, with focus on open-source projects:

<p align="center">
    <a href="/src/config/level/symfony"><img src="/docs/images/symfony.png"></a>
    <img src="/docs/images/space.png" width=20>
    <a href="/src/config/level/nette"><img src="/docs/images/nette.png" height="50"></a>
    <img src="/docs/images/space.png" width=20>
    <a href="/src/config/level/phpunit"><img src="/docs/images/phpunit.jpg"></a>
    <img src="/docs/images/space.png" width=20>
    <a href="/src/config/level/roave"><img src="/docs/images/roave.png"></a>
    <img src="/docs/images/space.png" width=20>
    <a href="/src/config/level/twig"><img src="/docs/images/twig.png"></a>
</p>

Rector can:

- Rename classes
- Rename class' methods
- Rename partial namespace
- Rename pseudo-namespace to namespace
- Add, replace or remove arguments
- Add typehints based on new types of parent class or interface
- And much more...

## Install

```bash
composer require --dev rector/rector 'dev-master' nikic/php-parser '4.0.x-dev'
```

Do you have old PHP or dependencies in conflict? Ok, [it is not a problem](/docs/HowUseWithOldPhp.md).

## How to Reconstruct your Code

### A. Prepared Sets

Featured open-source projects have **prepared sets**. You'll find them in [`/src/config/level`](/src/config/level).

Do you need to upgrade to **Symfony 4.0**, for example?

1. Run rector on your `/src` directory:

    ```bash
    vendor/bin/rector process src --level symfony40
    ```

    Which is a shortcut for using complete path with `--config` option:

    ```bash
    vendor/bin/rector process src --config vendor/rector/rector/src/config/level/symfony/symfony40.yml
    ```

    You can also use your **own config file**:

    ```bash
    vendor/bin/rector process src --config your-own-config.yml
    ```

2. Do you want to see the preview of changes first?

    Use the `--dry-run` option:

    ```bash
    vendor/bin/rector process src --level symfony33 --dry-run
    ```

### B. Custom Sets

1. Create `rector.yml` with desired Rectors:

    ```yml
    rectors:
        - Rector\Rector\Contrib\Nette\Application\InjectPropertyRector
    ```

2. Try Rector on your `/src` directory:

    ```bash
    vendor/bin/rector process src --dry-run
    ```

3. Apply the changes if you like them:

    ```bash
    vendor/bin/rector process src
    ```

## Simple setup with Dynamic Rectors

You don't have to always write PHP code. Many projects change only classes or method names, so it would be too much work for a simple task.

Instead you can use prepared **Dynamic Rectors** directly in `*.yml` config:

You can:

- **Replace a class name**

    ```yml
    # phpunit60.yml
    rectors:
        Rector\Rector\Dynamic\ClassReplacerRector:
            # old class: new class
            'PHPUnit_Framework_TestCase': 'PHPUnit\Framework\TestCase'
    ```

- **Replace some part of the namespace**

    ```yml
    # better-reflection20.yml
    rectors:
        Rector\Rector\Dynamic\NamespaceReplacerRector:
            # old namespace: new namespace
            'BetterReflection': 'Roave\BetterReflection'
    ```

- **Change a method name**

    ```yml
    rectors:
        Rector\Rector\Dynamic\MethodNameReplacerRector:
            # class
            'Nette\Utils\Html':
                # old method: new method
                'add': 'addHtml'

            # or in case of static methods calls

            # class
            'Nette\Bridges\FormsLatte\FormMacros':
                # old method: [new class, new method]
                'renderFormBegin': ['Nette\Bridges\FormsLatte\Runtime', 'renderFormBegin']
    ```

- **Change a property name**

    ```yml
    rectors:
        Rector\Rector\Dynamic\PropertyNameReplacerRector:
            # class:
            'PhpParser\Node\Param':
                # old property: new property
                'name': 'var'
    ```

- **Change a class constant name**

    ```yml
    rectors:
        Rector\Rector\Dynamic\ClassConstantReplacerRector:
            # class
            'Symfony\Component\Form\FormEvents':
                # old constant: new constant
                'PRE_BIND': 'PRE_SUBMIT'
                'BIND': 'SUBMIT'
                'POST_BIND': 'POST_SUBMIT'
    ```

- **Change parameters type hinting according to the parent type**

    ```yml
    rectors:
        Rector\Rector\Dynamic\ParentTypehintedArgumentRector:
            # class
            'PhpParser\Parser':
                # method
                'parse':
                    # parameter: typehint
                    'code': 'string'
    ```

- **Change a argument value**

    ```yml
    rectors:
        Rector\Rector\Dynamic\ArgumentReplacerRector:
            # class
            'Symfony\Component\DependencyInjection\ContainerBuilder':
                # method
                'compile':
                    # argument position
                    0:
                        # added default value
                        '~': false
                        # or remove completely
                        '~': ~
                        # or replace by new value
                        'Symfony\Component\DependencyInjection\ContainerBuilder\ContainerBuilder::SCOPE_PROTOTYPE': false
    ```

- **Replace the underscore naming `_` with namespaces `\`**

    ```yml
    rectors:
        Rector\Rector\Dynamic\PseudoNamespaceToNamespaceRector:
            # old namespace prefix
            - 'PHPUnit_'
            # exclude classes
            - '!PHPUnit_Framework_MockObject_MockObject'
    ```

- **Modify a property to method**

    ```yml
    rectors:
        Rector\Rector\Dynamic\PropertyToMethodRector:
            # type
            'Symfony\Component\Translation\Translator':
                # property to replace
                'locale':
                    # (prepared key): get method name
                    'get': 'getLocale'
                    # (prepared key): set method name
                    'set': 'setLocale'
    ```

- **Remove a value object and use simple type**

    ```yml
    rectors:
        Rector\Rector\Dynamic\ValueObjectRemoverRector:
            # type: new simple type
            'ValueObjects\Name': 'string'
    ```

    For example:

    ```php
    $value = new ValueObjects\Name('Tomas');

    // to

    $value = 'Tomas';
    ```

    ```php
    /**
     * @var ValueObjects\Name
     */
    private $name;

    // to

    /**
     * @var string
     */
    private $name;
    ```

    ```php
    public function someMethod(ValueObjects\Name $name) { ...

    // to

    public function someMethod(string $name) { ...
    ```

## Turn Magic to Methods

- **Replace `get/set` magic methods with real ones**

    ```yml
    rectors:
        Rector\Rector\MagicDisclosure\GetAndSetToMethodCallRector:
            # class
            'Nette\DI\Container':
                # magic method (prepared keys): new real method
                'get': 'getService'
                'set': 'addService'
    ```

    For example:

    ```php
    $result = $container['key'];

    // to

    $result = $container->getService('key');
    ```

    ```php
    $container['key'] = $value;

    // to

    $container->addService('key', $value);
    ```

- **Replace `isset/unset` magic methods with real ones**

    ```yml
    rectors:
        Rector\Rector\MagicDisclosure\UnsetAndIssetToMethodCallRector:
            # class
            'Nette\DI\Container':
                # magic method (prepared keys): new real method
                'isset': 'hasService'
                'unset': 'removeService'
    ```

    For example:

    ```php
    isset($container['key']);

    // to

    $container->hasService('key');
    ```

    ```php
    unset($container['key']);

    // to

    $container->removeService('key');
    ```

- **Replace `toString` magic method with real one**

    ```yml
    rectors:
        Rector\Rector\MagicDisclosure\ToStringToMethodCallRector:
            # class
            'Symfony\Component\Config\ConfigCache':
                # magic method (prepared key): new real method
                'toString': 'getPath'
    ```

    For example:

    ```php
    $result = (string) $someValue;

    // to

    $result = $someValue->someMethod();
    ```  

    ```php
    $result = $someValue->__toString();

    // to

    $result = $someValue->someMethod();
    ```

## Coding Standards are Outsourced

This package has no intention in formatting your code, as **coding standard tools handle this much better**.

We prefer [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard) that is already available:

```bash
# check
vendor/bin/ecs check --config vendor/rector/rector/ecs-after-rector.neon

# fix
vendor/bin/ecs check --config vendor/rector/rector/ecs-after-rector.neon --fix
```

## More Detailed Documentation

- [How to Create Own Rector](/docs/HowToCreateOwnRector.md)
- [Service Name to Type Provider](/docs/ServiceNameToTypeProvider.md)


## How to Contribute

Just follow 3 rules:

- **1 feature per pull-request**
- **New feature needs tests**
- Tests, coding standards and PHPStan **checks must pass**:

    ```bash
    composer complete-check
    ```

    Don you need to fix coding standards? Run:

    ```bash
    composer fix-cs
    ```

We would be happy to merge your feature then.
