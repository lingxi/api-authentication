# api-authentication

### 使用前

这个包是目前比较业界安全的加密实践，不仅仅作为 [lingxi api](https://open.lingxi360.com/category/docs) 的验证使用，有这种类似加密场景都可以使用这个 package，具体加密算法加文章最后。

### Install

```bash
composer require lingxi/api-authentication
```

### Usage

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

    // get response data
    $data = $response->getResponseData();

    // 大部分接口，response data 包含 data 和 meta 两部分，可以：
    $data = $response->getData();
    $meta = $response->getMeta();

    // 也支持作为对象属性获取
    $data = $response->data;
    $meta = $response->meta;
} catch (\Exception $e) {
    // deal with it...
}
```

### 签名验证方式

- 首先验证时间戳是否在当前时间 600s 内
- 其次验证 signature 是否正确

### signature 参数生成步骤

设所有需要发送的数据为集合M，在集合M中增加当前时间戳stamp，随机字符串noncestr以及机构的api_key，然后将集合M内非空参数值（或空数组）的参数按照参数名ASCII码从小到大排序（字典序），使用URL键值对的格式（即key1=value1&key2=value2…）拼接成字符串stringA。
对stringA进行sha256哈希计算，秘钥api_secret，得到signature值

### signature 参数验证步骤

参数名ASCII码从小到大排序（字典序）；
如果参数的值为空（或空数组）不参与签名；
参数名区分大小写；
验证调用返回或主动通知签名时，传送的signature参数不参与签名，将生成的签名与该signature参数值作校验。
接口可能会增加字段，验证签名时必须支持增加的扩展字段
