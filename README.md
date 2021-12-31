Multi language url manager
==========================
Multi language url manager

Installation
------------

Add to composer.json

```
"repositories":[
  {
    "type": "git",
    "url": "https://github.com/lexing2008/yii2-lang.git"
  }
]
```

and run

```
php composer.phar require --prefer-dist lexing2008/yii2-lang "dev-master"
```


Configuration
-----

Set in Configuration File:

```
'urlManager' => [
  'class' => 'xfg\lang\components\UrlManager',
  'showScriptName' => false,
  'enablePrettyUrl' => true,
  'enableStrictParsing' => true,
  'languages' => [
    'en',
    'ru',
  ]
]
```

Usage
-----

Add in any view:

```
use xfg\lang\widgets\LanguageSwitcher;
use xfg\bootstrapLanguages\BootstrapLanguagesAsset;
BootstrapLanguagesAsset::register($this);
echo LanguageSwitcher::widget();
```
