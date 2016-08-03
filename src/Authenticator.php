<?php

namespace Lingxi\Signature;

use Exception;
use Lingxi\Signature\Checker\VersionChecker;
use Lingxi\Signature\Checker\TimestampChecker;
use Lingxi\Signature\Checker\SignatureKeyChecker;
use Lingxi\Signature\Checker\SignatureValueChecker;
use Lingxi\Signature\Interfaces\AuthenticatorInterface;

class Authenticator implements AuthenticatorInterface
{
    protected $api_key;
    protected $api_secret;

    public function __construct($api_key = "", $api_secret = "")
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;

    }

    public function attempt($params)
    {
        return $this->checkTimestamp($params['stamp'])
             ->checkSignatureKey($params['api_key'])
             ->checkSignatureValue($params);
    }

    protected function checkTimestamp($timstamp)
    {
        if (TimestampChecker::check($timstamp)) {
            return $this;
        }
    }

    protected function checkSignatureKey($key)
    {
        if (! property_exists($this, 'api_secret') && ! method_exists($this, 'getSignatureApiSecret')) {
            throw new Exception('无法获取签名app_secret.');
        }

        if (SignatureKeyChecker::check($this->getSignatureApiSecret())) {
            return $this;
        }
    }

    protected function checkSignatureValue($params)
    {
        return SignatureValueChecker::check($params['signature'], $this->getSignatureValue($params));
    }

    public function getSignatureValue($params)
    {
        $paramsString = http_build_query($this->handleAllSignatureParamaters($params));

        return hash_hmac('sha256', $paramsString, $this->getSignatureApiSecret());
    }

    protected function handleAllSignatureParamaters($params)
    {
        $params = collect($params)->except(['signature', 'api_key'])->toArray();

        ksort($params, SORT_STRING);
        reset($params);

        return $params;
    }

    /**
     * @test
     */
    protected function getSignatureApiSecret()
    {
        if (property_exists($this, 'api_secret')) {
            return $this->api_secret;
        }
    }

    public function __set($key, $value)
    {
        $this->{$key} = $value;

        return $this;
    }

    public function getAuthParams($params){
        $noncestr = Helpers::createNonceStr();
        $params['stamp'] = time();
        $params['noncestr'] = Helpers::createNonceStr();
        $params['api_key'] = $this->api_key;
        $sign = $this->genSign($params);
        $params['signature'] = $sign;
        return $params;
    }

    public function verify($params){
        $need_to_check_sign = $params['signature'];
        unset($params["signature"]);
        $sign = $this->genSign($params);
        return ($sign == $need_to_check_sign);
    }

    /**
     * Sign签名生成方法
     *
     * @param array $para
     * @throws Exception
     * @return string
     */
    protected function genSign(array $para)
    {
        /**
         * a.除sign 字段外，对所有传入参数按照字段名的ASCII 码从小到大排序（字典序）后，
         * 使用URL 键值对的格式（即key1=value1&key2=value2…）拼接成字符串string1，
         * 注意： 值为空的参数不参与签名 ；
         */
        // 过滤不参与签名的参数
        $paraFilter = Helpers::paraFilter($para);
        // 对数组进行（字典序）排序
        $paraFilter = Helpers::argSort($paraFilter);
        // 进行URL键值对的格式拼接成字符串string1
        $str = Helpers::createLinkstring($paraFilter);
        /**
         * b. 在string1 最后拼接上key=Key(商户支付密钥 ) 得到stringSignTemp 字符串，
         * 并对stringSignTemp 进行md5 运算，再将得到的字符串所有字符转换为大写，得到sign 值signValue。
         */
        return hash_hmac('sha256', $str, $this->getSignatureApiSecret());
        // $sign = strtoupper(md5($string1));
        // return $sign;
    }


}
