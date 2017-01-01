<?php

namespace My\Flash;

use \Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Flash
 * @package My
 *
 * @method bool info(string $var) Magic shortcut call to set_message('info', $var)
 * @method bool success(string $var) Magic shortcut call to set_message('success', $var)
 * @method bool notice(string $var) Magic shortcut call to set_message('notice', $var)
 * @method bool warning(string $var) Magic shortcut call to set_message('warning', $var)
 * @method bool error(string $var) Magic shortcut call to set_message('error', $var)
 *
 */
class Flash {

    /**
     *
     * @param Session $session
     *
     */
    public $session;

    protected $message_types = [
        'info', 'success', 'notice', 'warning', 'error'
    ];

    public function __construct(Session $session) {

        $this->session = $session;

    }

    /**
     * Magic method to allow for setting messages like so:
     * Flash::info("message")  => Flash::set_message("info", "message")
     *
     * @param $method
     * @param $args
     *
     * @return bool
     * @throws FlashException
     */
    public function __call($method, $args) {

        if (in_array($method, $this->message_types)) {
            return $this->set_message($method, $args[0]);
        }

    }

    /**
     * Returns array of all flash messages.
     * @return array|mixed
     */
    public function get_messages() {
        if ($this->session->has('flash_messages')) {
            return $this->session->get('flash_messages');
        }
        return [];
    }

    /**
     * Adds a message to a particular flash message type.
     * @param $type
     * @param $message
     *
     * @return bool
     * @throws FlashException
     */
    public function set_message($type, $message) {
        if (!in_array($type, $this->message_types)) {
            throw new FlashException("Invalid flash message type");
        }
        if (!$message) {
            throw new FlashException("Flash message must not be empty");
        }

        $flash = $this->get_messages();
        if (!$flash) {
            $this->set_initial();
        }

        $flash[$type][] = $message;

        $this->session->set('flash_messages', $flash);

        return true;
    }

    /**
     * Initializes default flash message container
     */
    public function set_initial() {
        $flash = [];
        foreach ($this->message_types as $type) {
            $flash[$type] = [];
        }
        $this->session->set('flash_messages', $flash);
    }

    public function get_flash($type = null, $clear = true) {
        if ($type && !in_array('type', $this->message_types)) {
            throw new FlashException("Invalid flash message type");
        }

        if ($type) {
            $flash = $this->get_messages();
            $return_flash = [];
            $return_flash[$type] = $flash[$type];

            if ($clear) {
                $flash[$type] = [];
                $this->session->set('flash_messages', $flash);
            }

        } else {
            $return_flash = $this->get_messages();

            if ($clear) {
                $this->set_initial();
            }
        }

        return $return_flash;

    }

    public function get_flash_html($type = null, $clear = true) {
        $flash = $this->get_flash($type, $clear);
        $flash_html = [];

        foreach ($flash as $type => $message_list) {
            foreach ($message_list as $message) {
                $class = "flash-block flash-type-" . $type;
                $flash_html[] = "<div class='$class'><p>$message</p></div>";
            }
        }

        return implode($flash_html);
    }

}

class FlashException extends \Exception {}