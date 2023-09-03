# Limon
Dependency-less PHP Micro Framework with a focus on simplicity to get you
prototyping and delivering new APIs and Websites quickly. 

## Basic Usage
```php
<?php // example basic index.php

require_once __DIR__ . '/../../vendor/autoload.php';

(function() {
    $app = new Limon\App(
        new Limon\Kernel(
            new ActionResolver
        )
    );

    /** @var Psr\Http\Message\ServerRequestInterface $request */
    $request = captureServerRequest();

    // Reguster a handler, this can be replaced 
    // with middleware that sets the request-handler attribute
    // with a routing package
    $request = $request->withAttribute(
        'requst-handler', 
        fn(ServerRequestInterface $request) => new Response()
    );

    $res = $app->handle($request);

    Limon\emit($res);
})();
```

# Getting Started
This getting started is based on XAMPP
1. Require the composer package
```cli
composer require tylersriver/limon
```
2. Create index.php at Apache web root
3. Create .htaccess with below
```xml
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
</IfModule>
```

# Packages
Limon adheres to the PSR standards for request, response, middleware handling and can be used with
any compliant packages. There are some default packages wired to the example skeleton app [here](https://github.com/tylersriver/app)

## Actions
Yocto is built around the idea of [ADR](http://pmjones.io/adr/) and includes a base Action interface
that can be implemented for splitting your http routes into separate objects.

## Router

## Container

## Views
