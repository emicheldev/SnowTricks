<?php

namespace App\Services;

use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMail
{
    private $_userMail;

    private $_subjectMail;

    private $_template;

    private $_context;

    private $_mailer;
    
    /**
     * __construct
     *
     * @param  mixed $_mailer
     * @return void
     */
    public function __construct(MailerInterface $_mailer, $_userMail, $_subjectMail, $_template, $_context)
    {
        $this->_mailer = $_mailer;
        $this->_userMail = $_userMail;
        $this->_subjectMail = $_subjectMail;
        $this->_template = $_template;
        $this->_context = $_context;

    }
    
    /**
     * sendNotification
     *
     * @return void
     */
    public function sendNotification()
    {

        $message = (new TemplatedEmail())
				->from('no-reply@snow-tricks.com')
                ->to($this->_userMail)
                ->subject($this->_subjectMail)
                ->htmlTemplate($this->_template)
                ->context([
                    'user' => $this->_context,
                ]);

            $this->_mailer->send($message);

    }
}
