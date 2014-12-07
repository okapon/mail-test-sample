<?php

namespace AppBundle\Service;

use Phake;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use AppBundle\Entity\User;

class MailServiceIntegrationTest extends WebTestCase
{
    protected $mailService;

    public function setUp()
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();
        $this->mailService = $container->get('app.mail_service');
    }

    /**
     * @test
     */
    public function sendRegistrationMail()
    {
        $fromEmail = 'info@example.com';
        $userEmail = 'user.mail@example.com';
        $userName = '田中 太郎';
        $activationUrl = 'http://example.com/activation?code=abcdefg';

        $user = Phake::mock(User::class);
        Phake::when($user)->getUsername()->thenReturn($userName);
        Phake::when($user)->getEmail()->thenReturn($userEmail);

        // test
        $this->mailService->sendRegistrationMail($user);

        // 送信されたメッセージのインスタンスを取得
        $messages = $this->mailService->getMessages();

        $this->assertCount(1, $messages);

        // 1通目だけテスト
        $message = $messages[0];
        $this->assertInstanceOf('Swift_Message', $message);

        $expected = [
            'from' => $fromEmail,
            'to' => $userEmail,
            'subject' => '[xxx サービス] ご登録ありがとうございます',
            'body' => [
                $userName,
                $activationUrl,
            ]
        ];

        $this->verifyMail($message, $expected);
    }

    protected function verifyMail(\Swift_Message $message, array $expected)
    {
        // From & To アドレスを検証
        $this->assertEquals($expected['from'], key($message->getFrom()));
        $this->assertEquals($expected['to'], key($message->getTo()));

        // Subject
        $this->assertEquals($expected['subject'], $message->getSubject());

        // Body
        foreach ($expected['body'] as $body) {
            $this->assertContains($body, $message->getBody());
        }
    }
}
