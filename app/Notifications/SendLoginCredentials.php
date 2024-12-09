<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendLoginCredentials extends Notification
{
    protected $username;
    protected $email;
    protected $password;

    /**
     * Create a new notification instance.
     *
     * @param string $username
     * @param string $email
     * @param string $password
     */
    public function __construct($username, $email, $password)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view(
                'emails.login_credentials',
                [
                    'username' => $this->username,
                    'email' => $this->email,
                    'password' => $this->password,
                ]
            )
            ->subject('بيانات تسجيل الدخول الخاصة بنظام الأستبيان');
    }
}
