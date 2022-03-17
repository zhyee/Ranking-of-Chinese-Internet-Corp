<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/1
 * Time: 19:10
 */

namespace Rrclic\Library;

class Helper
{
    /**
     * @param string $file
     * @param string $content
     * @return bool|int
     */
    public static function writeToFile($file, $content)
    {
        $dir = dirname($file);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                throw new \RuntimeException('无法创建文件夹：' . $dir);
            }
        }
        $fp = fopen($file, 'w');
        if (!$fp) {
            throw new \RuntimeException('无法创建文件或文件不可写：' . $file);
        }
        return fwrite($fp, $content);
    }
}
