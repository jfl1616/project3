<?php


namespace Bolzen\Src\Model\Email;


use Bolzen\Core\Model\Model;
use PHPMailer\PHPMailer\PHPMailer;

class EmailModel extends Model
{
    private $phpmailer;

    /*
     * Set up a Mail Host
     */
    public function __construct()
    {
        parent::__construct();
        $this->phpmailer = new PHPMailer();
        $this->phpmailer->Host = getenv("EMAIL_HOST");
        $this->phpmailer->Port = getenv("EMAIL_PORT");
        $this->phpmailer->Mailer = getenv("EMAIL_MAILER");
        $this->phpmailer->SMTPAuth = getenv("EMAIL_SMTPAUTH");
        $this->phpmailer->SMTPSecure = getenv("EMAIL_SMTPSECURE");
        $this->phpmailer->SMTPDebug = getenv("EMAIL_SMTPDEBUG");
        $this->phpmailer->Username = getenv("EMAIL_USERNAME");
        $this->phpmailer->Password = getenv("EMAIL_PASSWORD");
    }
    /*
     * Prepare the email with the subject and message, then send it to the recipient.
     */
    public function send(string $subject, string $message, string $to):bool {
        if(empty($subject) || empty($message) || empty($to)){
            $this->setError("Subject, message, and to are required.");
            return false;
        }
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        $this->phpmailer->isHTML(true);
        $this->phpmailer->setFrom("Jose.F.Lopez@outlook.com", "Jose Lopez Jr.");
        $this->phpmailer->addAddress($to);
        $this->phpmailer->Subject = $subject;
        $this->phpmailer->Body = $message;

        if(!$this->phpmailer->send()){
            $this->setError("Unable to send the email at this time.");
            return false;
        }
        return true;
    }
}