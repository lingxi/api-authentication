<?php

namespace Linxgi\Signature;

use Exception;
use GuzzleHttp\TransferStats;
use InvalidArgumentException;
use Illuminate\Support\Collection;
use Lingxi\Signature\Authenticator;
use GuzzleHttp\Client as GuzzleHttpClient;

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
    protected $requestUrls  = [];

    public function __construct(
        $apiKey = null,
        $apiSecret = null,
        $mode = self::MODE_API_KEY,
        $gateway = 'http://apix.lingxi360.com',
        $timeOut = 3.0
    ) {
        $this->http = new GuzzleHttpClient([
            'base_uri' => $gateway,
            'time_out' => $timeOut,
            'on_stats' => function (TransferStats $stats) {
                $this->requestUrls[] = $stats->getEffectiveUri()->__toString();
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

    public function getEffectiveUrl()
    {
        return $this->requestUrls[count($this->requestUrls) - 1];
    }

    public function getRequestUrls()
    {
        return $this->requestUrls;
    }

    public function get($api, $params = [])
    {
        return $this->call('get', $api, $params);
    }

    public function post($api, $params = [])
    {
        return $this->call('post', $api, $params);
    }

    public function put($api, $params = [])
    {
        return $this->call('put', $api, $params);
    }

    public function patch($api, $params = [])
    {
        return $this->call('patch', $api, $params);
    }

    public function delete($api, $params = [])
    {
        return $this->call('delete', $api, $params);
    }

    public function call($method, $api, $params = [])
    {
        if (! in_array($method, ['get', 'post', 'put', 'patch', 'delete'])) {
            throw new InvalidArgumentException($method . ' not available.');
        }

        if ($method == 'get') {
            $key = 'query';
        } else {
            $key = 'form_params';
        }

        $response = $this->http->{$method}(
            $this->addVersionPrefix($api), [
                $key => $this->combineParams($params),
            ]
        );

        return $this->dealResponse($response);
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
        return new Collection(array_merge(['id' => $this->data['id']], $this->data['attributes']));
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

    public function __get($name)
    {
        return new Collection($this->responseData->get($name));
    }
    
    public function setVersion($version)
    {
        $this->version = $version;
    }

    protected function addVersionPrefix($api)
    {
        return '/' . $this->version . $api;
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
}
