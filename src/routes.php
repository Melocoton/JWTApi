<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

return function (App $app) {
    $container = $app->getContainer();

    $app->post('/login', function (Request $request, Response $response, array $args) use ($container) {
        $data = $request->getBody()->getContents();
        $datos = json_decode($data);

        $user = $datos->user;
        $pass = $datos->pass;

        if ($user === "admin" && $pass === "1234"){
            $token = JWT::encode(['user' => $user],'supersecretkeyyoushouldnotcommittogithub');
            return $this->response->withJson(['error' => false, 'data' => [
                "token" => $token
            ], 'message' => 'Login succesfull']);
        }else{
            return $this->response->withJson(['error' => true, 'data' => null, 'message' => 'These credentials do not match our records.']);
        }

    });

    $app->get('/api/user', function (Request $request, Response $response, array $args) use ($container) {
        return $this->response->withJson(['error' => false, 'data' => $request->getAttribute('decoded_token_data'), 'message' => 'Data']);
    });

    $app->post('/api/data', function (Request $request, Response $response, array $args) use ($container) {
        return $this->response->withJson(['error' => false, 'data' => json_decode($request->getBody()->getContents()), 'message' => 'Data']);
    });

};
