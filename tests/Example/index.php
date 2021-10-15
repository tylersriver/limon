<?php

use Yocto\Request;
use Yocto\Container;
use Yocto\Views;
use Yocto\Router;
use Yocto\App;
use Yocto\Response;
use Yocto\Tests\App\Actions\FooAction;
use Yocto\Tests\App\Actions\SampleAction;

use function Yocto\emit;
use function Yocto\redirect;
use function Yocto\html;
use function Yocto\render;
use function Yocto\success;

require_once __DIR__ . '/../../vendor/autoload.php';

$config = [ 
    'ENVIRONMENT' => 'DEVELOPMENT',
    'root' => '//localhost/',          
    'log_dir' => __DIR__ . '\\log\\'  
];

error_reporting($config['ENVIRONMENT'] === 'DEVELOPMENT'  ? E_ALL : 0); 
ini_set('display_errors', $config['ENVIRONMENT'] === 'DEVELOPMENT'  ? 1 : 0);

$container = new Container([
    'Config' => $config,
    Views::class => new Views(__DIR__ . '/../App/Views')
]);

$app = App::create($container);

$r = new Router($container);
$r->get('/api/user/:id/role', fn(Request $request) => success([]));
$r->get('/', fn() => redirect('/view/home'));
$r->get('/view/home', fn() => html(render()));
$r->get('/api/test', SampleAction::class);
$r->addGroup('/group', function(Router $r) {
    $r->addGroup('/api', function(Router $r) {
        $r->get('/:id', fn(Request $request) => success([]));
        $r->post('/:id/test/:role', SampleAction::class);
    });
    $r->get('/place', fn(Request $request) => success([]));
});
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