<?php

namespace GHank\WSNotice;

class EventHandler
{
    public static function callEvent($frame, $fromData)
    {
        $eventArr = explode('/',$fromData->event);

        if (count($eventArr) > 2) {
            $actionName = array_pop($eventArr);
            $controllerName = ucfirst(array_pop($eventArr));
            $classPrefix = implode('\\',$eventArr);
            $eventClassName = '\\app\\event\\' . $classPrefix . '\\' . $controllerName;

            var_dump(class_exists($eventClassName));
            if(class_exists($eventClassName) && method_exists($eventClassName, $actionName)) {
                lib\Event::registerEvent($classPrefix, $controllerName, $actionName);
                $eventClassName::$actionName($frame->fd, $fromData);
            }
        }
    }
}
