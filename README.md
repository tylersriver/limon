# yocto
Dependency-less PHP Micro Framework with a focus on simplicity to get you up
and prototyping new APIs and Websites quickly. Comes out of the box
with Routing, Middleware, View rendering, and a Container with dependency auto-wiring. 
```php
require_once __DIR__ . '/../../vendor/autoload.php';

// 1. Create App
$app = Yocto\App::create();

// 2. Create a Route
$r = new Yocto\Router; 
$r->get('/test', fn() => Yocto\success(['message' => 'success']));
$app->setRouter($r);

// 3. Grab and Handle request
$request = Yocto\Request::fromGlobals();
$response = $app->handle($request);

// 4. Emit the response
Yocto\emit($response);
```

# Getting Started
This getting started is based on XAMPP
1. Require the composer package
```cli
composer require tylersriver/yocto
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

# Features
Yocto comes ready to go with a lot of features for you to quickly
write Web apps.

## Actions
Yocto is built around the idea of [ADR](http://pmjones.io/adr/) and includes a base Action class
that can be extended and used by the router for robust request handling.
```php
#[Route(Route::GET, '/foo/action')]                   // <-- You can register routes via the Route attribute, see Router section
class FooAction extends Yocto\Action
{
    public function __construct(Class $dependency)   // <-- Add constructor for auto-wired dependencies
    {
    }

    #[Parameter('foo', Parameter::GET, '^bar$')]      // <-- The Parameter attribute to autofill from request and validate
    #[Required]                                       // <-- Required attribute enforces parameters to exist in request
    protected string $var;

    public function action(): Response                // <-- Implement action method and return response
    {
        return success(['message' => 'success']);
    }
}
```

## Router

## Container

## Views
