# PowerRouter Plugin

[![Build Status](https://travis-ci.org/eberfreitas/cakephp-power-router.svg?branch=master)](https://travis-ci.org/eberfreitas/cakephp-power-router) [![Coverage Status](https://img.shields.io/coveralls/eberfreitas/cakephp-power-router.svg)](https://coveralls.io/r/eberfreitas/cakephp-power-router?branch=master)

PowerRouter is a plugin that extends how your default Router can be used. It
enables three new features:

* Add named routes
* Manipulate the params from a matched route
* Matches a route only if it conforms to a given condition

## Requirements

* CakePHP 2.x (tested on 2.4 and 2.5 but should work on every 2.x release)
* PHP 5.3 or later (should work on 5.2 but it is not tested)

## Installation

**Using [Composer](http://getcomposer.org/)**

Add the plugin to your project's `composer.json` - something like this:

```javascript
{
    "require": {
        "eberfreitas/cakephp-power-router": "dev-master"
    }
}
```

Because this plugin has the type `cakephp-plugin` set in it's own
`composer.json`, composer knows to install it inside your `/Plugins` directory,
rather than in the usual vendors file. It is recommended that you add
`/Plugins/PowerRouter` to your .gitignore file.
Why? [read this](http://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md).

**Manual**

* Download this: http://github.com/eberfreitas/cakephp-power-router/zipball/master
* Unzip that download
* Copy the resulting folder to app/Plugins
* Rename the folder you just copied to `PowerRouter`

**GIT Submodule**

In your app directory type:

```bash
git submodule add git://github.com/eberfreitas/cakephp-power-router.git plugins/PowerRouter
git submodule init
git submodule update
```

**GIT Clone**

In your plugin directory type:

```bash
git clone git://github.com/eberfreitas/cakephp-power-router.git PowerRouter
```

## Usage

PowerRouter is a custom route class that extends on the CakeRoute. This way you
can use it to define your routes and take advantage of it's features. Using it
will replace both `match` and `parse` methods.

The `match` method is used to handle reverse routing and `parse` is used to
parse requests. You can read more about custom route classes on the
[CakePHP book](http://book.cakephp.org/2.0/en/development/routing.html).

In order to use the features from PowerRouter, first you need to load the plugin
adding the following line in your `app/Config/bootstrap.php`:

```php
CakePlugin::load('PowerRouter');
```

Later, import the library into your "app/Config/routes.php" file like this:

```php
App::uses('PowerRoute', 'PowerRouter.Lib');
```

After that you can start to define your routes like this:

```php
Router::connect(
    '/path',
    array('controller' => 'pages', 'action' => 'view'),
    array(
        'routeClass' => 'PowerRoute',
        'routeName' => 'main-path',
        'callback' => $callback,
        'condition' => $condition
    )
);
```

But, if you are planning on using PowerRoute a lot, you can use the helper class
`PowerRouter` in order to make declaring new routes a little bit easier. In that
case, instead of importing the 'PowerRoute' lib, import the 'PowerRouter' lib
like this (mind the "r"):

```php
App::uses('PowerRouter', 'PowerRouter.Lib');
```

Now you can do something like this:

```php
PowerRouter::connect(
    'main-path',
    '/path'
    array('controller' => 'pages', 'action' => 'view'),
    array(
        'callback' => $callback,
        'condition' => $condition
    )
);
```

Here is how the `PowerRouter::connect` method is described:

```php
PowerRouter::connect($name, $route, $defaults = array(), $options = array()) { /*...*/ }
```

Where:

* `$name` is the name you want to give to this route. Ideally it should be a unique
  identifier
* `$route` is your route path, something like `/:controller/:action/:id` and so
  on, just like in regular routes
* `$defaults` is an array describing the default route parameters, just like in
  regular routes
* `$options` works just like the options param from regular routes as well. Here
  you can also declare two new keys: `condition` and `callback`. We will take a
  look at those pretty soon.

### Using named routes

So, the first thing you can do with PowerRouter is to define named routes. This
will make it easier and faster to match routes when doing any kind of route
matching. Let's see an example:

```php
PowerRouter::connect(
    'about',
    '/about-us',
    array('controller' => 'pages', 'action' => 'display', 'about')
);
```

Now, if you want to link to this page on your views, you can do something like
this:

```php
echo $this->Html->link('About us', array('routeName' => 'about'));
```

That will match the right route. You can also combine params:

```php
// In your routes.php

PowerRouter::connect(
    'product-view',
    '/product/:slug/:id',
    array('controller' => 'products', 'action' => 'view')
);

// In your views

echo $this->Html->link(
    $product['Product']['title'],
    array(
        'routeName' => 'product-view',
        'slug' => $product['Product']['slug'],
        'id' => $product['Product']['id']
    )
);
```

### Using callbacks

With callbacks you can manipulate your route's params before it is used by the
application. When a route is parsed it returns an array with the params it will
use such as the controller that was matched, the action, among other things.
Using a callback you can inject or modify information into that array. The
callback can be anything that `is_callable`, including closures. Let's take a
look at an example:

```php
// routes.php

$transformRoute = function ($params) {
    $params['action'] = 'dash_' . $params['action'];

    return $params;
};

PowerRouter::connect(
    'route',
    '/dashboard/:controller/:action/*',
    array(),
    array('callback' => $transformRoute)
);
```

Now, when someone hits `/dashboard/user/view/123` you will "redirect" the
request to the `UserController::dash_view` method. The callback is executed and
effectively changes the params being used on that request.

It's really important that you return the `$params` variable at the end of the
callback function, as that will be used by the application from now on.

### Using conditional callbacks

Another form of callback that PowerRouter enables is the `condition` one. With
that callback you can block a route from being used by any conditions you
define in that callback. The function should return a boolean value representing
the validity of that route. Example:

```php
$condition = function () {
    return Configure::read('route') === true;
};

$routes = PowerRouter::connect(
    'test',
    '/findamatch',
    array(
        'controller' => 'main',
        'action' => 'view'
    ), array(
        'condition' => $condition
    )
);
```

Depending on the value of the `route` configuration key, the `test` route will
be used or not. With that you can dynamically mold your routes to respond
according to a given environment or set of conditions.

Alternatively you can pass an array of callback functions that will be executed
in order until any of the them deny access to that route.

# License

The MIT License (MIT)

Copyright (c) 2014 Éber Freitas Dias

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.