<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/9/27
 * Time: 22:12
 */

namespace app\index\model;


/**
 * Class HttpRequest
 * @method
 * */
class HttpRequest
{
    public static $url;
    public static $method;
    public static $https;
    public static $cookies;
    public static $headers;
    public static $data;
    public static $response;
    private static $req;
    public static $error;

    /**
     * @param $url
     * @param array $data
     * @param string $method
     * @param array $headers
     * @param array $cookies
     * @param bool $https
     */
    public static function create($url, $data = [], $method = "GET", $headers = [], $cookies = [], $https = false)
    {
        self::$url = $url;
        self::$method = strtoupper($method); //转为全大写
        self::$headers = $headers;
        self::$cookies = $cookies;
        self::$https = $https;
        self::$data = $data;
        self::init();
    }

    /**
     * @desc 构造请求
     */
    private static function init()
    {
        self::$req = curl_init(self::$url);
        curl_setopt(self::$req, CURLOPT_RETURNTRANSFER, 1);
        if (self::$https) {
            curl_setopt(self::$req, CURLOPT_SSL_VERIFYPEER, false);
        }
        if (!empty(self::$headers)) {
            curl_setopt(self::$req, CURLOPT_HTTPHEADER, self::$headers);
        }
        if (!empty(self::$cookies)) {
            curl_setopt(self::$req, CURLOPT_COOKIE, self::$cookies);
        }
        if (self::$method == "POST") {
            curl_setopt(self::$req, CURLOPT_POST, self::$data);
        }
    }

    public static function GET($url, $data = [], $headers = [], $cookies = "", $https = false)
    {
//        初始化请求
        self::create($url, $data, "GET", $headers, $cookies, $https);
    }

    public static function POST($url, $data, $headers = [], $cookies = "", $https = false)
    {
        self::create($url, $data, "POST", $headers, $cookies, $https);
    }

    public static function upload($url, $filename)
    {

    }

    /**
     * @param string $url
     * @param array $data
     * @return string
     */
    public static function decodeUrl($url, $data)
    {
        $str_data = "";
        array_walk($data, function ($value, $key) use (&$str_data) {
            $str_data = $str_data . $key . "=" . $value . "&";
        });
        $url = $url . "?" . $str_data;
        return $url;
    }

    public static function exec()
    {
        $response = curl_exec(self::$req);
        self::$response = $response;
        self::$error = curl_error(self::$req);
    }

    public static function error()
    {
        return self::$error;
    }

    public static function close()
    {
        curl_close(self::$req);
    }

    public static function download()
    {

    }

}