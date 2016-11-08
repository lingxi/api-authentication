<?php

namespace Lingxi\Signature;

use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\TransferStats;
use Lingxi\Signature\Authenticator;

/**
 * Api Client
 * 发起 Api 请求，获取结果
 *
 * Usage:
 *     new Client(...)->order('id', 'asc')->get('/contct/list', [...])->getResponseData();
 */
class Client
{
    const MODE_API_KEY = 'api_key';
    const MODE_PARTNER = 'partner_key';

    protected $http;
    protected $authenticator;

    protected $version = 'v1';

    protected $responseData;

    protected $orderBy       = null;
    protected $orderSequence = null;
    protected $effectiveUrl  = '';

    public function __construct(
        $apiKey = null,
        $apiSecret = null,
        $gateway = null,
        $mode = self::MODE_API_KEY,
        $timeOut = 3.0
    ) {
        $gateway = ($gateway ?: $this->getGateWay()) . '/' . $this->version . '/';

        $this->http = new GuzzleHttpClient([
            'base_uri' => $gateway,
            'time_out' => $timeOut,
            'on_stats' => function (TransferStats $stats) {
                $this->effectiveUrl = $stats->getEffectiveUri()->__toString();
            },
        ]);

        $this->authenticator = new Authenticator($apiKey, $apiSecret, $mode);
    }

    protected function combineParams($params)
    {
        if ($this->orderBy !== null) {
            $params['order_by']       = $this->orderBy;
            $params['order_sequence'] = $this->orderSequence;
        }

        // reset params after every request
        $this->resetParams();

        return $this->authenticator->getAuthParams($params);
    }

    protected function resetParams()
    {
        $this->orderBy = $this->orderSequence = null;
    }

    private function dealResponse($response)
    {
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Lingxi Api Response Status Is Not 200');
        }

        if (!$data = json_decode($response->getBody(), true)) {
            throw new Exception('Lingxi Api return null');
        }

        if (isset($data['status_code']) && $data['status_code'] >= 10000) {
            throw new Exception('Lingxi Api return error: ' . $data['error']);
        }

        $this->responseData = collect($data);

        return $this;
    }

    public function getEffectiveUrl()
    {
        return $this->effectiveUrl;
    }

    /**
     * Make request
     */

    public function get($api, $params = [])
    {
        $response = $this->http->get($api, ['query' => $this->combineParams($params)]);

        return $this->dealResponse($response);
    }

    public function post($api, $params = [])
    {
        $response = $this->http->post($api, ['form_params' => $this->combineParams($params)]);

        return $this->dealResponse($response);
    }

    public function put($api, $params = [])
    {
        $response = $this->http->put($api, ['form_params' => $this->combineParams($params)]);

        return $this->dealResponse($response);
    }

    /**
     * Order utility
     */

    public function order($by, $sequence = 'desc')
    {
        $this->orderBy       = $by;
        $this->orderSequence = $sequence;

        return $this;
    }

    /**
     * Get reqponse data
     */

    public function getResponseData()
    {
        return $this->responseData;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * 将 id 合并到 attributes 里，方便外面使用
     *
     * 适用于 show a resoure 时使用
     */
    public function getItem()
    {
        return collect(array_merge(['id' => $this->data['id']], $this->data['attributes']));
    }

    public function __get($name)
    {
        return collect($this->responseData->get($name));
    }
}
