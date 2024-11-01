<?php
namespace mp_ssv_general;
if (!defined('ABSPATH')) {
    exit;
}

class Message
{
    const NOTIFICATION_MESSAGE = 'notification';
    const ERROR_MESSAGE = 'error';
    const SOFT_ERROR_MESSAGE = 'soft-error';

    public $message;
    public $type;

    /**
     * Message constructor.
     *
     * @param string $message is the
     * @param string $type
     */
    public function __construct($message, $type = Message::NOTIFICATION_MESSAGE)
    {
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * @return string with the message.
     */
    public function __toString()
    {
        return $this->message;
    }

    public function getHTML()
    {
        switch ($this->type) {
            case Message::SOFT_ERROR_MESSAGE: {
                $class = '';
                break;
            }
            case Message::ERROR_MESSAGE: {
                $class = 'error';
                break;
            }
            case Message::NOTIFICATION_MESSAGE:
            default: {
                $class = 'success';
                break;
            }
        }
        ob_start();
        ?>
        <div class="col s12 card-panel <?= esc_html($class) ?>" style="padding: 10px;">
            <?= esc_html($this->message) ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
