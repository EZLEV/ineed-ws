<?php
// Routes

use Slim\Http\Request;
use Slim\Http\Response;
use Firebase\JWT\JWT;

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

$app->post('/ws/0/signup', function (Request $request, Response $response) {
    $data = $request->getBody()->getContents();

    return $response->write(createCustomToken('abc123456789abcde', array("hu3" => true)));
});

$service_account_email = "firebase-adminsdk-lwsop@default-project-d4f76.iam.gserviceaccount.com";
$private_key = "-----BEGIN PRIVATE KEY-----\nMIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQC/b07W2Ii/1pRl\nM4EWvaDWPAkFMkZyYQdxRNOcILmqnb3n9HKCpr2++xPTMMrm6+ftH+kgJzVCOJVu\nNzKsMLImCXG7yW5FeJ0m2fHt7W/Lq3GrjQqss9OTIRPyyutWdZA3ZNUJzQZ5517v\nPhNGiBKgbZVkhamSeKWq4o5jbN4hPEGo8ZMWSMdNZs3GRvmgUk87R9Ms1SpA1yCR\nX2+vOKb76wh+WlhEPKOMrDo0BfiNdEzCRQ1TZrvnuDR1GE192xkZSKECc0yRcjWx\nPEtBD7rkrfxoHN3RWEa0m4czljnMZ3GzmTF8RWIkq6Xjy0cWnmHPfuy/wNs/17fi\nNWgi++TDAgMBAAECggEAHVQXv4moFb/xtzl3Tv0ZXYkQFrg3m8Fqyvsw8kv/Nfj5\nxcYpHwQdsNs1k9b3Vv6QZz9Kz47CNZWGqz6QqFnDiVlMD+mR19ndNb0ROBL23Dy1\nawNuPbxFL1bTgBB/kpzrTdlIXDqJgfalEEEx4c3qEKMJTr+9lX+fXflcuDXPKEcX\nZMPrk26CuPWv5Zz2smk38YpLpRnamPrbE2v8EU8khfh49aFiXl/EaM7EfXp8ppSk\n3TZ3TTgv/Fmx4puWzUOQ+OJm4Nrc7/1Pw3aIQ45p80qLSkM9yT9RDmq6GggXavUc\nuMEPB+2tQFdiqkb7gYp5kIVyFC3KL29B6py8ZLvG7QKBgQDzIO+96bbA5dH5QCpW\n/NYtGowg42TqEZ5joRI5OOCCWL3hp5/DASYrXakIqxL55sYLSb9vGMkgy3eUg3dI\nmK9T22REzM+YYHZkefBfGyLoAhZwt7vp8wJb5g/tf/M4WZ7rAZbSJxX2quK6+KCM\nvLdJ9JlwCSTLMOG9HcD23+KQ5QKBgQDJkcbzuRSql3QsKUgjOMvDaePWGYRzqqcM\nWpvVC2STuUt+nxYQvHYmyIygRhWnfSBRGpvqJAnfYH1nKuu6FCoyR6KPJT0pSfOM\nw1RyAPaJsQbF6mvWpkdGO29F5mvbhU5Q0vtuFTvvI/JS/f4Tb6onPhDUPY3w5Uz3\nKbYVaS/MhwKBgQDivSG4TED6bpo+yF90FoGrOKncdhUD0gCTy4BSSz/db+NNkeUF\nmIm/Qa7Ffb3JvzWNC27zrfrMkdRodZ6F3pcMLnu5SgSEh9mB5NKN76HDG0dQQZmH\nGfFmQQ0zofLy52m0oxvDy13JWB7w3bPk7I5G800xVeWxdVng4+G0mqESHQKBgQCV\n0Lm0kE1h9aus3wn009Pu7BchHForthzuu7GzCQK6ITCRbiByVADlFo4e2bhigkew\nwDw+LIcB9a9/LJGD/lTWhhO2nRD2TTat2sg666hR8rd8Bp4cLf4vnyE3LOnhgRUS\nUZ361eFz/p2vPYTIYWhwPls3xIBpGaS419Gz3DhgpQKBgQCv0r3S7gmqSPhEKwZE\nxBb6fK8bexHY0GwDcY41YrDbpJqybR9YrJEMk/cQ2I0qvff5cKJAdwq7O2MwvzS2\n/kRAYYPtnfwb7+Ch/BJmGPeXwTQKKuP582W0iqRFIyMrtlDm5vKSCqe6fXdtvERu\nP5jHsFrFp1Mqariex7hfTe8BGg==\n-----END PRIVATE KEY-----\n";

function createCustomToken($uid, $claims) {
  global $service_account_email, $private_key;

  $now_seconds = time();
  $payload = array(
    "iss" => $service_account_email,
    "sub" => $service_account_email,
    "aud" => "https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit",
    "iat" => $now_seconds,
    "exp" => $now_seconds+(60*60),  // Maximum expiration time is one hour
    "uid" => $uid,
    "claims" => $claims,
    "firebase" => array ("email" => "meuemail@com.br")
  );
/*array(
      "premium_account" => $is_premium_account
    )*/
  return JWT::encode($payload, $private_key, "RS256");
}