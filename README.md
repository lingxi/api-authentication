# api-authentication

### Install

```bash
<<<<<<< HEAD
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
