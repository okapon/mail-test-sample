<?php

namespace AppBundle\Tests\Mock\Service;

use AppBundle\Service\MailService;

/**
 * TestMailService.
 *
 * 送信メールの内容をテストするため、Swift_Messageを内部に保持し
 * 後から取りだしすことをできるようにしたテスト用のクラス
 *
 */
class TestMailService extends MailService
{
    /**
     * @var array Swift_Message[]
     */
    protected $messages = [];

    /**
     * {@inheritDoc}
     */
    public function sendMessage(\Swift_Message $message)
    {
        $this->messages[] = $message;

        return parent::sendMessage($message);
    }

    /**
     * getMesssage
     *
     *
     * @return array Swift_Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
