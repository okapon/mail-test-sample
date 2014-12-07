<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;

class MailService
{
    protected $mailer;
    protected $twig;

    /**
     * Constructor
     *
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $twig
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendRegistrationMail(User $user)
    {
        // 認証URLを作成する、サンプルコードなのでここでは固定で記述しています
        $activationUrl = 'http://example.com/activation?code=abcdefg';

        $body = $this->render('AppBundle:Mail:register.txt.twig', [
            'user' => $user,
            'activationUrl' => $activationUrl,
        ]);
        $subject = '[xxx サービス] ご登録ありがとうございます';
        $fromEmail = 'info@example.com';
        $userEmail = $user->getEmail();

        $message = \Swift_Message::newInstance()
            ->setFrom($fromEmail)
            ->setTo($userEmail)
            ->setSubject($subject)
            ->setBody($body)
        ;

        return $this->sendMessage($message);
    }

    /**
     * sendMessage
     *
     * @param \Swift_Message $message
     * @return void
     */
    protected function sendMessage(\Swift_Message $message)
    {
        $this->mailer->send($message);
    }

    /**
     * render
     *
     * @param string $template
     * @param array $vars
     * @return string
     */
    protected function render($template, array $vars = [])
    {
        return $this->twig->loadTemplate($template)->render($vars);
    }
}
