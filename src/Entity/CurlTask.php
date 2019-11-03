<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/14
 * Time: 0:14
 */

namespace Rrclic\Entity;

class CurlTask
{
    // 任务ID
    private $id;

    // curl句柄
    private $curlHandler;

    // 最大重试次数
    private $maxRetryCount = 1;

    // 执行失败次数
    private $failCount = 0;

    /**
     * curl请求执行成功时的回调
     * @var callable $successCall
     */
    private $successCall;

    /**
     * curl请求执行失败时的回调
     * @var callable $errorCall
     */
    private $errorCall;

    /**
     * 任务是否执行结束
     * @var bool
     */
    private $isOver = false;

    // 任务是否已经销毁
    private $isDestroyed = false;

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setCurlHandler($curlHandler)
    {
        $this->curlHandler = $curlHandler;
        return $this;
    }

    public function getCurlHandler()
    {
        return $this->curlHandler;
    }

    public function setMaxRetryCount(int $count)
    {
        if ($count < 1) {
            $count = 1;
        }
        $this->maxRetryCount = $count;
        return $this;
    }

    public function getMaxRetryCount()
    {
        return $this->maxRetryCount;
    }

    public function incrFailCount()
    {
        return (++$this->failCount);
    }

    public function getFailCount()
    {
        return $this->failCount;
    }

    public function isCanRun()
    {
        if (is_null($this->curlHandler) || !$this->curlHandler) {
            return false;
        }

        if ($this->failCount >= $this->maxRetryCount) {
            return false;
        }
        return true;
    }

    public function __clone()
    {
        if ($this->isDestroyed) {
            throw new \RuntimeException('调用过destroy方法的任务不允许克隆');
        }
        $this->failCount = 0;
        $this->isOver = false;
    }

    public function setSuccessCall(callable $call)
    {
        $this->successCall = $call;
        return $this;
    }

    public function setErrorCall(callable $call)
    {
        $this->errorCall = $call;
        return $this;
    }

    public function error($errMsg)
    {
        if (is_callable($this->errorCall)) {
            call_user_func($this->errorCall, $this, $errMsg);
        }
        $this->isOver = true;
    }

    public function success()
    {
        if (is_callable($this->successCall)) {
            call_user_func($this->successCall, $this);
        }
        $this->isOver = true;
    }

    public function isOver()
    {
        return $this->isOver;
    }

    /**
     * 销毁任务
     */
    public function destroy()
    {
        curl_close($this->curlHandler);
        $this->curlHandler = null;
        $this->failCount = 0;
        $this->isDestroyed = true;
    }

    public function __destruct()
    {
        if (!$this->isDestroyed) {
            $this->destroy();
        }
    }
}