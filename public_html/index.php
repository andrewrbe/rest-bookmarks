<?php
use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader->add('Models', __DIR__ . '/../app/');


$app = new Silex\Application();


if (getenv('APPLICATION_ENV') == 'development') {
    $app['debug'] = true;
}

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/../var/database.sqlite',
    ),
));
$app->register(new Models\Bookmark());
$app->register(new Models\Comment());


//получить список 10 последних добавленных Bookmark
$app->get('/api/bookmarks', function () use ($app) {
    return $app->json($app['model.bookmark']->getLast());
});


//получить Bookmark (с комментариями) по Bookmark.url. Если такого ещё нет, не создавать.
$app->get('/api/bookmark', function (Request $request) use ($app) {
    return $app->json($app['model.bookmark']->getByUrl($request->get('url')));
});


//добавить Bookmark по url и получить Bookmark.uid. Если уже есть Bookmark с таким url, не добавлять ещё один, но получить Bookmark.uid.
$app->put('/api/bookmark', function (Request $request) use ($app) {
    $url = $request->get('url');
    if (!$url) {
        return $app->json(['error' => 'URL required']);
    }
    return $app->json($app['model.bookmark']->getOrCreateUid($url));
});


//добавить Comment к Bookmark (по uid) и получить Comment.uid
$app->put("/api/comment", function (Request $request) use ($app) {

    $text = $request->get('text');
    if (empty($text)) {
        return $app->json(['error' => 'Text required'], 500);
    }

    $result = $app['model.comment']->add($request->get('uid'), $text, $request->getClientIp());
    return $app->json($result, isset($result['error']) ? 500 : 200);

});


//изменить Comment.text по uid (если он добавлен с этого же IP и прошло меньше часа после добавления)
$app->post("/api/comment", function (Request $request) use ($app) {
    $text = $request->get('text');
    if (empty($text)) {
        return $app->json(['error' => 'Text required'], 500);
    }

    $result = $app['model.comment']->edit($request->get('uid'), $text, $request->getClientIp());
    return $app->json($result, isset($result['error']) ? 500 : 200);

});


//удалить Comment по uid (если он добавлен с этого же IP и прошло меньше часа после добавления)
$app->delete("/api/comment", function (Request $request) use ($app) {
    $result = $app['model.comment']->delete($request->get('uid'), $request->getClientIp());
    return $app->json($result, isset($result['error']) ? 500 : 200);
});


$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return $app->json(['error' => $e->getMessage()], 500);
    } else {
        return $app->json(['error' => 'An error occurred while processing the request'], 500);
    }
});


$app->run();