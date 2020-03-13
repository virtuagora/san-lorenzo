<?php

namespace App\Service;

class SwiftMailerService
{
    protected $mailer;
    protected $sender;
    
    public function __construct($transport, $options)
    {
        $this->sender = ['test@test.com' => 'Testing'];
        if ($transport == 'null') {
            $transport = new \Swift_NullTransport();
        } elseif ($transport == 'smtp') {
            $security = isset($options['security']) ? $options['security'] : null;
            $transport = new \Swift_SmtpTransport(
                $options['host'],
                $options['port'],
                $security
            );
            $transport->setUsername($options['username']);
            $transport->setPassword($options['password']);
            $this->sender = $options['sender'];
        } elseif ($transport == 'sendmail') {
            $transport = new \Swift_SendmailTransport($options['command']);
        } elseif ($transport == 'mail') {
            $transport = new \Swift_MailTransport();
        }
        $this->mailer = new \Swift_Mailer($transport);
    }

    public function send($message)
    {
        return $this->mailer->send($message);
    }

    public function sendMail($subject, $to, $body, $mime = 'text/plain')
    {
        $message = new \Swift_Message($subject);
        $message->setTo($to);
        if (is_array($body)) {
            $first = true;
            foreach ($body as $part) {
                if ($first) {
                    $message->setBody($part);
                    $first = false;
                } else {
                    $message->addPart($part);
                }
            }
        } else {
            $message->setBody($body, $mime);
        }
        $message->setFrom($this->sender);
        return $this->send($message);
    }
    
    public function getMailer()
    {
        return $this->mailer;
    }
}