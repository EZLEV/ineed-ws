<?php
// Routes

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

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
