<?php

use Yocto\Request;
use Yocto\Container;
use Yocto\Views;
use Yocto\Router;
use Yocto\App;
use Yocto\Tests\App\Actions\HomeAction;
use Yocto\Tests\App\Actions\SampleAction;

use function Yocto\cachedRouter;
use function Yocto\emit;

require_once __DIR__ . '/../../vendor/autoload.php';

$config = [ 
    'ENVIRONMENT' => 'DEVELOPMENT',
    'root' => '//localhost/',          
    'log_dir' => __DIR__ . '\\log\\',
];

error_reporting($config['ENVIRONMENT'] === 'DEVELOPMENT'  ? E_ALL : 0); 
ini_set('display_errors', $config['ENVIRONMENT'] === 'DEVELOPMENT'  ? 1 : 0);

$container = new Container([
    'Config' => $config,
    Views::class => new Views(__DIR__ . '/../App/Views')
]);

$app = App::create($container);

$r = cachedRouter(function(Router $r) {
    $r->loadFromAttributes(__DIR__ . '/../App/Actions', 'Yocto\Tests\App\Actions');

    $r->get('/', HomeAction::class);
    $r->group('/group', function(Router $r) {
        $r->group('/api', function(Router $r) {
            $r->post('/:id/test/:role', SampleAction::class);
        });
    });

    return $r;
}, [
    'cacheEnabled' => false,
    'cacheDir' => __DIR__,
    'version' => 1
]);

$app->setRouter($r);

$app->add(new class extends Yocto\Middleware 
{
    public function process(Request $request): Yocto\Response
    {
        return $this->next->process($request);
    }
});

$request = ( Request::fromGlobals() )->withParsedBody();
$response = $app->handle($request);

emit($response);