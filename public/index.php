<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', '*');
});

use Slim\Http\Request;
use Slim\Http\Response;
#use Slim\Http\UploadedFile;

$app->post('/ws/0/optmize', function (Request $request, Response $response) {
    \Tinify\setKey("2rFPiSylwP8pZUe17DMUdw_GnQ9KWtDk");
    $id = rand(); 
    $contentType = $request->getHeader('Content-Type');
    $data = $request->getBody()->getContents();

    $originalFile = $id . ".jpg";
    $optimizedFile = $id . ".optimized.jpg";

    file_put_contents($originalFile, $data);

    $source = \Tinify\fromFile($originalFile);
    $resized = $source->resize(array(
        "method" => "scale",
        "width" => 500
    ));

    $resized->toFile($optimizedFile);

    chmod($originalFile, 0777);
    unlink($originalFile);

    $b64image = base64_encode(file_get_contents($optimizedFile));

    chmod($optimizedFile, 0777);
    unlink($optimizedFile);

    return $response->write("data:image/jpeg;base64," . $b64image);
});

// Run app
$app->run();
