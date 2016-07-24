<?php

namespace Olorin\Support;


class Flash
{
    /**
     * Flash an info message to the session.
     *
     * @param $msg
     * @param string $title
     * @param string $type
     * @return mixed
     */
    public function message($msg, $title = 'Info', $type = 'info')
    {
        return $this->flashMessage($title, $msg, $type);
    }

    /**
     * FLash a success message to the session.
     *
     * @param $msg
     * @param string $title
     * @return mixed
     */
    public function success($msg, $title = 'Success')
    {
        return $this->flashMessage($title, $msg, 'success');
    }


    /**
     * Flash a warning message to the session.
     *
     * @param $msg
     * @param string $title
     * @return mixed
     */
    public function warning($msg, $title = 'Warning')
    {
        return $this->flashMessage($title, $msg, 'warning');
    }


    /**
     * Flash an error message to the session.
     *
     * @param $msg
     * @param string $title
     * @return mixed
     */
    public function error($msg, $title = 'Error')
    {
        return $this->flashMessage($title, $msg, 'error');
    }

    /**
     * Flash an alert message to the session.
     *
     * @param $msg
     * @param string $title
     * @param string $type
     * @return mixed
     */
    public function alert($msg, $title = 'Info', $type = 'info')
    {
        return $this->flashMessage($title, $msg, $type, 'flash_message_alert');
    }

    /**
     * Flash a confirm message to the session.
     *
     * @param $msg
     * @param string $title
     * @param string $type
     * @return mixed
     */
    public function confirm($msg, $title = 'Info', $type = 'info')
    {
        return $this->flashMessage($title, $msg, $type, 'flash_message_confirm');
    }

    /**
     * Flash a message to the session based on parameters.
     *
     * @param $title
     * @param $message
     * @param $type
     * @param string $key
     * @return mixed
     */
    private function flashMessage($title, $message, $type, $key = 'flash_message'){
        return session()->flash($key, [
            'title'     => $title,
            'message'   => $message,
            'type'      => $type
        ]);
    }
}