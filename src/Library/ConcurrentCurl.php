<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/22
 * Time: 23:13
 */


namespace Rrclic\Library;

class ConcurrentCurl
{
    protected $mh;

    /**
     * @var int 最大并发数
     */
    protected $maxConcurrency = 16;

    /**
     * @var int 最多重试次数
     */
    protected $maxTryCount = 3;

    protected $taskPoolCount = 0;

    protected $successCallback;

    protected $errorCallback;

    protected $errorCounter = [];

    protected $urls;

    protected $urlsBack;

    protected $initialized = false;

    protected $curlPool = [];

    protected $curlOpts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    ];

    public function __construct(array $urls)
    {
        $this->urls = $urls;
    }

    /**
     * 向url列表尾部追加新的单元
     * @param $url
     * @return $this
     */
    public function appendUrl($url)
    {
        if (is_string($url)) {
            $url = [$url];
        }
        $this->urls = array_merge($this->urls, $url);
        return $this;
    }

    /**
     * 向url列表头部追加新的单元
     * @param $url
     * @return $this
     */
    public function prependUrl($url)
    {
        if (is_string($url)) {
            array_unshift($this->urls, $url);
        } else {
            foreach ($url as $value)
            {
                array_unshift($this->urls, $value);
            }
        }
        return $this;
    }

    /**
     * 设置最大的并发请求数
     * @param $concurrency
     * @return $this
     */
    public function setMaxConcurrency($concurrency)
    {
        $this->maxConcurrency = $concurrency;
        return $this;
    }

    /**
     * 设置请求失败时的最大重试次数，首次失败为1，依次类推
     * @param $count
     * @return $this
     */
    public function setMaxTryCount($count)
    {
        $this->maxTryCount = $count;
        return $this;
    }

    /**
     * 设置全局curl参数
     * @param $opts
     * @return $this
     */
    public function setCurlOpts($opts)
    {
        $this->curlOpts += $opts;
        return $this;
    }

    /**
     * 设置curl执行成功时的回调函数，函数参数是返回的http响应内容
     * @param $callback
     * @return $this
     */
    public function success($callback)
    {
        $this->successCallback = $callback;
        return $this;
    }

    /**
     * 设置curl执行失败时的回调函数，函数参数是curl错误码
     * @param $callback
     * @return $this
     */
    public function error($callback)
    {
        $this->errorCallback = $callback;
        return $this;
    }

    protected function init()
    {
        $this->mh = curl_multi_init();
        $this->addCurlTask();
        $this->initialized = true;
    }

    protected function addCurlTask()
    {
        $count = 0;
        while (count($this->urls) && $this->taskPoolCount < $this->maxConcurrency) {
            $url = array_shift($this->urls);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if ($this->curlOpts) {
                curl_setopt_array($ch, $this->curlOpts);
            }
            curl_multi_add_handle($this->mh, $ch);
            $this->curlPool[(int)$ch] = $url;
            $this->taskPoolCount++;
            $count ++;
        }
        return $count;
    }

    protected function processCurlResult($info)
    {
        $ch = $info['handle'];
        $url = $this->curlPool[(int)$ch];
        if ($info['result'] !== CURLE_OK) {
            if (isset($this->errorCounter[$url])) {
                $this->errorCounter[$url]++;
            } else {
                $this->errorCounter[$url] = 1;
            }
            if ($this->errorCounter[$url] < $this->maxTryCount) {
                $this->prependUrl($url);
            }
            call_user_func($this->errorCallback, $info['result'], $ch);
        } else {
            $html = curl_multi_getcontent($ch);
            call_user_func($this->successCallback, $html, $ch);
        }
        curl_multi_remove_handle($this->mh, $ch);
        unset($this->curlPool[(int)$ch]);
        $this->taskPoolCount--;
    }

    public function run()
    {
        $this->init();
        do {
            curl_multi_exec($this->mh, $remainTaskCount);
            if ($remainTaskCount < $this->taskPoolCount) {
                do {
                    $info = curl_multi_info_read($this->mh, $remainMsgCount);
                    if ($info) {
                        $this->processCurlResult($info);
                    }
                } while ($remainMsgCount);

                $this->addCurlTask();
            }
            curl_multi_select($this->mh);

        } while ($this->taskPoolCount > 0);
        curl_multi_close($this->mh);
    }
}
