<?php

declare(strict_types=1);

namespace MNIB\Guzzle;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use function is_numeric;
use function max;
use function microtime;
use function round;
use function sprintf;
use function usleep;

/**
 * Middleware to throttle requests in Guzzle.
 * Useful when you're limited to an explicit number of requests per second.
 */
class ThrottleMiddleware
{
    /** @var int */
    private $lastRequestTime = 0;

    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, $options) use ($handler) {
            if (isset($options['throttle_delay'])) {
                if (!is_numeric($options['throttle_delay'])) {
                    throw new InvalidArgumentException(
                        sprintf('Invalid value for throttle_delay usage in %s.', self::class)
                    );
                }

                $delay = $this->getDelay($options['throttle_delay']);

                if ($delay > 0) {
                    $this->throttle($delay);
                }

                $this->setLastRequestTime((int)microtime(true));
            }

            return $handler($request, $options);
        };
    }

    public function getLastRequestTime(): int
    {
        return $this->lastRequestTime;
    }

    public function setLastRequestTime(int $lastRequestTime): void
    {
        $this->lastRequestTime = $lastRequestTime;
    }

    /**
     * Calculate the remaining delay.
     *
     * @param int $throttleDelay
     *
     * @return int
     */
    protected function getDelay(int $throttleDelay): int
    {
        $lastRequestTime = $this->getLastRequestTime();
        $requestTime = (int)microtime(true);

        return $lastRequestTime ? max(0, $throttleDelay - 1000 * ($requestTime - $lastRequestTime)) : 0;
    }

    /**
     * Sleep till the next request is ready to go.
     *
     * @param int $delay
     */
    protected function throttle(int $delay): void
    {
        $delay = (int)max(0, round($delay * 1000));

        usleep($delay);
    }
}
