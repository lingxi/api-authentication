# api-authentication

### Install

```bash
composer require lingxi/api-authentication
```

### Usage

> 仅仅验证以及获取签名, 不具有 http 请求的功能

```php
use Lingxi\Signature\Authenticator;

require __dir__ . '/vendor/autoload.php';

$auther = new Authenticator('key', 'secret');
$data = [
    'id' => 'cawzyvopker1gdxeqx82w6qm2x5l73n8',
];
echo 'http://api.lingxi.com/v1/partner/cms/content/show?' . http_build_query($auther->getAuthParams($data)) . PHP_EOL;
```

> 为了方便使用，我们封装了 Api Client，你可以使用它直接调用接口，获取结果

```php
use Lingxi\Signature\Client;

$apiClient = new Client(
    $partner->partner_key,
    $partner->partner_secret
);

try {
    $response = $apiClient->order('id', 'asc')->get('/contact/show');
} catch (\Exception $e) {
    // deal with it...
}

// get response data
$data = $response->getResponseData();

// 大部分接口，response data 包含 data 和 meta 两部分，可以：
$data = $response->getData();
$meta = $response->getMeta();

// 也支持作为对象属性获取
$data = $response->data;
$meta = $response->meta;
```

