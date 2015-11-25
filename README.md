Mailer
======
PHP and SMTP Mailer 

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist samsher-bentray/yii2-mailer "*"
```

or add

```
"samsher-bentray/yii2-mailer": "*"
```

to the require section of your `composer.json` file.

Configuration
-------------

Once the extension is installed, add following code to your application configuration :

```php
return [
    //....
    'components' => [

            ...

            'email' => 'samsher\mailer\Mail',
            
            ...

    ],
];
```

Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= \samsher\mailer\AutoloadExample::widget(); ?>```