<?php

namespace JPaylor\BTMyDonate;

use JPaylor\BTMyDonate\Models;
use Symfony\Component\DomCrawler;
use Concat\Http\Middleware\Logger;

class Scrape {
    private $log;
    private $http;

    /**
     * Scrape constructor.
     *
     * @param array $params
     */
    public function __construct($params = [])
    {
        $this->cookieJar = new \GuzzleHttp\Cookie\CookieJar();

        $handlerStack = \GuzzleHttp\HandlerStack::create();

        if ( ! empty($params['log']) && is_a($params['log'], 'Psr\Log\LoggerInterface')) {
            $this->log = $params['log'];

            // Setup guzzle logging
            $middleware = new Logger($this->log);
            $middleware->setRequestLoggingEnabled(false);
            $handlerStack->push(
                $middleware
            );
        }

        $this->http = new \GuzzleHttp\Client(['handler' => $handlerStack]);
    }

    /**
     * Log
     *
     * @param string $level
     * @param string $message
     * @param array $data
     */
    private function log($level, $message, $data = [])
    {
        if ( ! empty($this->log)) {
            $this->log->$level($message, $data);
        }
    }

    /**
     * Get Fundraising
     *
     * @param string $fundraisingPage
     * @return array
     */
    public function getFundraising($fundraisingPage)
    {
        $this->log('info', 'Getting fundraising details', ['page' => $fundraisingPage]);

        // Get fundraising page
        $fundraisingPageUrl = sprintf('https://mydonate.bt.com/fundraisers/%s', $fundraisingPage);
        $fundraisingPage = $this->http->request('GET', $fundraisingPageUrl, []);
        $fundraisingPageDom = new DomCrawler\Crawler((string) $fundraisingPage->getBody());

        $fundraising = new Models\Fundraising();

        // Get fundraising total
        $totalRaised = $fundraisingPageDom->filter('.block-donate-now .text-primary')->first()->text();
        preg_match('/(\d+\.\d+)/', $totalRaised, $matches);
        if (empty($matches[1])) {
            throw new \Exception('Could not find fundraising total from content retrieved from page');
        }
        $fundraising->setTotalRaised($matches[1]);

        // Get fundraising target
        $target = $fundraisingPageDom->filter('.block-donate-now .text-muted')->first()->text();
        preg_match('/(\d+\.\d+)/', $target, $matches);
        if (empty($matches[1])) {
            throw new \Exception('Could not find fundraising target from content retrieved from page');
        }
        $fundraising->setTarget($matches[1]);

        return $fundraising;
    }
}