Multi language url manager
==========================
Multi language url manager

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist xfg/yii2-lang "*"
```

or add

```
"xfg/yii2-lang": "*"
```

to the require section of your `composer.json` file.


Configuration
-----

Set in Configuration File:

```php
'urlManager' => [
  'class' => 'xfg\lang\UrlManager',
  'showScriptName' => false,
  'enablePrettyUrl' => true,
  'enableStrictParsing' => true,
  'displaySourceLanguage' => true,
  'languages' => [
    'en' => 'en-US',
    'ru' => 'ru-RU',
  ]
]```

Usage
-----

In any view add:

```php
<?php use xfg\lang\LanguageSwitcher ?>
<?= LanguageSwitcher::widget() ?>```