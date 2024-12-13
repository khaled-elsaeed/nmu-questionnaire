<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

use Illuminate\Queue\SerializesModels;

class SendLoginCredentials extends Mailable
{
    use Queueable, SerializesModels;

    protected $username;
    protected $email;
    protected $password;

    /**
     * Create a new message instance.
     *
     * @param string $username
     * @param string $email
     * @param string $password
     */
    public function __construct(string $username, string $email, string $password)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('بيانات تسجيل دخول لنظام الاستبيانات')
                    ->view('emails.login_credentials') // Ensure this Blade view exists
                    ->with([
                        'username' => $this->username,
                        'email' => $this->email,
                        'password' => $this->password,
                    ]);
    }
}

