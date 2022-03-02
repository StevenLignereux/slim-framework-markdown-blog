<?php

declare(strict_types=1);

use DI\Container;
use MarkdownBlog\ContentAggregator\ContentAggregatorFactory;
use MarkdownBlog\ContentAggregator\ContentAggregatorInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Extra\Intl\IntlExtension;
use Slim\App;
use Mni\FrontYAML\Parser;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$container->set('view', function ($c) {
    $twig = Twig::create(__DIR__ . '/../resources/templates');
    $twig->addExtension(new IntlExtension());
    return $twig;
});

$container->set(
    ContentAggregatorInterface::class,
    fn () => (new ContentAggregatorFactory())->__invoke([
        'path' => __DIR__ . '/../data/posts',
        'parser' => new Parser()
    ])
);


AppFactory::setContainer($container);
$app = AppFactory::create();
$app->add(TwigMiddleware::createFromContainer($app));

$app->map(['GET'], '/', function (Request $request, Response $response, array $args) {
    return $this->get('view')->render($response, 'index.html.twig');
});

$app->run();
