# Usage
    $handlerStack = HandlerStack::create();
    $handlerStack->push(new ThrottleMiddleware());
    
    $httpClient = new \GuzzleHttp\Client(array(
        'handler' => $handlerStack,
        'throttle_delay' => 1000 // in milliseconds
    ));