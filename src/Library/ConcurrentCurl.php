<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/22
 * Time: 23:13
 */


namespace Rrclic\Library;

use Rrclic\Entity\CurlTask;

class ConcurrentCurl
{
    protected $mh;

    /**
     * @var int 最大并发数
     */
    protected $maxConcurrency = 16;

    // 任务队列
    protected $taskQueue;

    // 任务执行池
    protected $taskPool;

    protected $initialized = false;

    public function __construct()
    {
        $this->taskQueue = new \SplQueue();
        $this->taskPool = [];
    }

    /**
     * 把任务添加到队列的尾部，任务将较晚执行
     * @param CurlTask $task
     * @return $this
     */
    public function appendTask(CurlTask $task)
    {
        $this->taskQueue->push($task);
        return $this;
    }

    /**
     * 把任务添加到队列的头部，任务将优先执行
     * @param CurlTask $task
     * @return $this
     */
    public function prependTask(CurlTask $task)
    {
        $this->taskQueue->unshift($task);
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

    protected function init()
    {
        $this->mh = curl_multi_init();
        $this->addTaskToPool();
        $this->initialized = true;
    }

    protected function addTaskToPool()
    {
        while (!$this->taskQueue->isEmpty() && count($this->taskPool) < $this->maxConcurrency) {
            /** @var CurlTask $task */
            $task = $this->taskQueue->shift();
            if ($task->isCanRun()) {
                $curl = $task->getCurlHandler();
                if (!isset($this->taskPool[(int)$curl]) || !$this->taskPool[(int)$curl] instanceof \SplQueue) {
                    $this->taskPool[(int)$curl] = new \SplQueue();
                    curl_multi_add_handle($this->mh, $curl);
                }
                //关联相同curl句柄的任务
                $this->taskPool[(int)$curl]->enqueue($task);
            } else {
                $task->error('task无法运行，请确认是否设置了curl句柄');
            }
        }
    }

    protected function processCurlResult($info)
    {
        $curl = $info['handle'];
        while (!$this->taskPool[(int)$curl]->isEmpty()) {
            /** @var CurlTask $task */
            $task = $this->taskPool[(int)$curl]->dequeue();
            if ($info['result'] !== CURLE_OK) {
                if ($task->incrFailCount() < $task->getMaxRetryCount()) {
                    $this->appendTask($task);
                } else {
                    $task->error(sprintf('任务：%s失败次数已达%d次', $task->getId(), $task->getMaxRetryCount()));
                }
            } else {
                $task->success();
            }
        }
        curl_multi_remove_handle($this->mh, $curl);
        unset($this->taskPool[(int)$curl]);
    }

    public function bootstrap()
    {
        $this->init();
        do {
            curl_multi_exec($this->mh, $remainTaskCount);
            if ($remainTaskCount < count($this->taskPool)) {
                do {
                    $info = curl_multi_info_read($this->mh, $remainMsgCount);
                    if ($info) {
                        $this->processCurlResult($info);
                    }
                } while ($remainMsgCount);

                $this->addTaskToPool();
            }
            curl_multi_select($this->mh);

        } while (count($this->taskPool) > 0);
        curl_multi_close($this->mh);
    }
}
