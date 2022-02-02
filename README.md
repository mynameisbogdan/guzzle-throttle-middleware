# Usage
```php
$handlerStack = \GuzzleHttp\HandlerStack::create();
$handlerStack->push(new \MNIB\Guzzle\ThrottleMiddleware());

$httpClient = new \GuzzleHttp\Client(array(
    'handler' => $handlerStack,
    'throttle_delay' => 1000 // in milliseconds
));
```
