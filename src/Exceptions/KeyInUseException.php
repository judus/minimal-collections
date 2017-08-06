<?php namespace Maduser\Minimal\Collections\Exceptions;

/**
 * Class KeyInUseException
 *
 * @package Maduser\Minimal\Collections
 */
class KeyInUseException extends \Exception
{
    /**
     * @return mixed
     */
    public function getMyFile()
    {
        if ($this->isMessageObject()) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->myMessage->getFile();
        }

        return debug_backtrace()[3]['file'];
    }

    /**
     * @return mixed
     */
    public function getMyLine()
    {
        if ($this->isMessageObject()) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->myMessage->getLine();
        }

        return debug_backtrace()[3]['line'];
    }
}