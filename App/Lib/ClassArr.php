<?php
namespace App\Lib;

/**
 * 做一些反射机制有关的处理
 * Class ClassArr
 * @package App\Lib
 */
class ClassArr {

    /**
     * @return array
     */
    public function uploadClassStat()
    {
        return [
            'image' => "App\Lib\Upload\Image",
            'video' => "App\Lib\Upload\Video"
        ];
    }

    /**
     * 获得类的实例化对象
     * @param $type // 上传的文件类型如：image, video
     * @param $supportedClass // 类
     * @param array $params // 实例化类时，传递的参数
     * @param bool $needInstance
     * @return bool|object
     * @throws \ReflectionException
     */
    public function initClass($type, $supportedClass, $params=[], $needInstance=true)
    {
        if (!array_key_exists($type, $supportedClass)) {
            return false;
        }

        $className = $supportedClass[$type];

        //如果需要实例化，则返回类的实例，否则返回原类名（不需要实例化的情况）
        return $needInstance ? (new \ReflectionClass($className))->newInstanceArgs($params) : $className;
    }
}