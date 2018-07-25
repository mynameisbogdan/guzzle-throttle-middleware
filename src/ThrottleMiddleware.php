<?php
namespace MNIB\Guzzle;

use Psr\Http\Message\RequestInterface;

/**
 * Middleware to throttle requests in Guzzle.
 * Useful when you're limited to an explicit number of requests per second.
 */
class ThrottleMiddleware
{
    /** @var float */
    private $lastRequestTime;

    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, $options) use ($handler) {
            if (!empty($options['throttle_delay'])) {
                if (!is_numeric($options['throttle_delay'])) {
                    throw new \InvalidArgumentException(
                        sprintf('Invalid value for throttle_delay usage in %s.', __CLASS__)
                    );
                }

                $delay = $this->getDelay($options['throttle_delay']);

                if ($delay > 0) {
                    $this->throttle($delay);
                }

                $this->setLastRequestTime(microtime(true));
            }

            return $handler($request, $options);
        };
    }

    /**
     * @return float
     */
    public function getLastRequestTime(): float
    {
        return $this->lastRequestTime;
    }

    /**
     * @param float $lastRequestTime
     */
    public function setLastRequestTime(float $lastRequestTime): void
    {
        $this->lastRequestTime = $lastRequestTime;
    }

    /**
     * Calculate the remaining delay.
     *
     * @param int $throttleDelay
     * @return float
     */
    protected function getDelay(int $throttleDelay): float
    {
        $lastRequestTime = $this->getLastRequestTime();
        $requestTime = microtime(true);

        return $lastRequestTime ? max(0, $throttleDelay - (1000 * ($requestTime - $lastRequestTime))) : 0;
    }

    /**
     * Sleep till the next request is ready to go.
     *
     * @param int $delay
     */
    protected function throttle(int $delay): void
    {
        $delay = max(0, round($delay * 1000));

        usleep($delay);
    }
}
