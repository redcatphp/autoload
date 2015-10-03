# Autoload - PHP native dependency manager
--------

 Autoload is a simple and concise PHP [Autoloader](http://php.net/autoload) based on universal conventions.  
 No more need of annoying "require\_once" at start of each php file. When needed by php code the class will be dynamicaly loaded and no more load of unused class, increasing performances in same time.   
 This component is able to autoload all modern PHP frameworks and libraries like [Zend](https://github.com/zendframework), [Symfony](https://symfony.com), [PEAR](https://pear.php.net), [Aura](http://auraphp.com) or many others.

- [PSR-4](http://www.php-fig.org/psr/psr-4/)
- [PSR-0](http://www.php-fig.org/psr/psr-0/) (retrocompat)
- classMap API
- [include\_path](http://php.net/manual/fr/ini.core.php#ini.include-path) support
- [HHVM](https://en.wikipedia.org/wiki/HipHop_Virtual_Machine) hack
- empty namespace support for root autoload path
- cache for checked class\_exists

Methods usage
-------------

### simple [facade](https://en.wikipedia.org/wiki/Facade_pattern) API using global instance

```php
use Wild\Autoload\Autoload;
/* register "MyNamespace\SubSpace" prefix to "myDirectory/src/myNamespacePath" directory */
Autoload::register('myDirectory/src/myNamespacePath','MyNamespace\SubSpace');

/* register the containing file directory as a root directory for autoload */
Autoload::register(__DIR__);
/* equivalent */
Autoload::register(__DIR__,'');
```

### get global instance

```php
$autoload = Autoload::getInstance();
```

### register and unregister to [SPL](http://php.net/manual/en/book.spl.php) stack

```php
$autoload->splRegister();
$autoload->splUnregister();
```

### add namespaces

```php
$autoload->addNamespace('Prefix\Of\My\Namespace','target/directory');
$autoload->addNamespace('Prefix\Of\My\Namespace2',[
	'target/directory1',
	'target/directory2',
]);
$autoload->addNamespaces([
	'Prefix\Of\My\Namespace'=>'target/directory/for/my/namespace',
	'Prefix\Of\My\Namespace2'=>[
		'target/directory1',
		'target/directory2',
	]
]);
```

### useIncludePath

```php
$autoload->useIncludePath(true); //default param to true but default property to false
```

### useCache

```php
$autoload->useCache(false); //default param and property to true
```

### addClass and addClassMap

```php
$autoload->addClass('My\Class','path/of/myclass.php');
$autoload->addClassMap([
	'My\Class'=>'path/of/myclass.php',
	'My\Class2'=>'path/of/myclass2.php',
]);
```

*[PSR]:     PHP Standard Recommendation
*[SPL]:     Standard PHP Library