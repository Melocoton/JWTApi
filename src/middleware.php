<?php

use Slim\App;

return function (App $app) {
    $app->add(function($request, $response, $next) {
        $route = $request->getAttribute("route");
    
        $methods = [];
    
        if (!empty($route)) {
            $pattern = $route->getPattern();
    
            foreach ($this->router->getRoutes() as $route) {
                if ($pattern === $route->getPattern()) {
                    $methods = array_merge_recursive($methods, $route->getMethods());
                }
            }
            //Methods holds all of the HTTP Verbs that a particular route handles.
        } else {
            $methods[] = $request->getMethod();
        }
    
        $response = $next($request, $response);
    
    
        return $response->withHeader("Access-Control-Allow-Methods", implode(",", $methods))
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization');
    });

    // e.g: $app->add(new \Slim\Csrf\Guard);
    $app->add(new \Tuupola\Middleware\JwtAuthentication([
        "path" => "/api", /* or ["/api", "/admin"] */
        "attribute" => "decoded_token_data",
        "secret" => "supersecretkeyyoushouldnotcommittogithub",
        "algorithm" => ["HS256"],
        "secure" => false,
        "error" => function ($response, $arguments) {
            $data["error"] = "true";
            $data["data"] = null;
            $data["message"] = $arguments["message"];
            return $response
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
    ]));
};
