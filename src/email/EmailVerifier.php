<?php
declare(strict_types=1);

namespace DeveloperNode\Email;

class EmailVerifier implements IEmailVerifier
{
    private $fromName;
    private $fromDomain;
    private $port;
    private $maxConnectionTimeout;
    private $maxStreamTimeout;
    
    public function __construct()
    {
        $this->fromName = 'noreply';
        $this->fromDomain = 'example.com';
        $this->port = 25;
        $this->maxConnectionTimeout = 30;
        $this->maxStreamTimeout = 5;
    }
    
    public function CheckEmail(string $email): CheckEmailStatus
    {
        $result = false;
        if (!$this->IsValid($email))
            return CheckEmailStatus::INVALID_EMAIL();
        
        list($user, $domain) = $this->ParseEmail($email);
        
        $mxs = $this->GetMXrecords($domain);
        $fp = false;
        $timeout = ceil($this->maxConnectionTimeout / count($mxs));
        
        foreach ($mxs as $host) {
            if ($fp = @stream_socket_client("tcp://" . $host . ":" . $this->port, $errno, $errstr, $timeout)) {
                stream_set_timeout($fp, $this->maxStreamTimeout);
                stream_set_blocking($fp, true);
                $code = $this->SockGetResponseCode($fp);
                if ($code == '220')
                    break;
                else {
                    fclose($fp);
                    $fp = false;
                }
            }
        }
        
        if (!$fp)
            return CheckEmailStatus::INVALID_EMAIL_DOMAIN();
        
        $this->Sockquery($fp, "HELO " . $this->fromDomain);
        $this->Sockquery($fp, "MAIL FROM: <" . $this->fromName . '@' . $this->fromDomain . ">");
        $code = $this->Sockquery($fp, "RCPT TO: <" . $user . '@' . $domain . ">");
        $this->Sockquery($fp, "RSET");
        $this->Sockquery($fp, "QUIT");
        
        fclose($fp);
        
        if ($code == '250' || $code == '450' || $code == '451' || $code == '452')
            return CheckEmailStatus::SUCCESS();
        
        return CheckEmailStatus::EMAIL_NOT_FOUND();
    }
    
    private function IsValid(string $email)
    {
        return (false !== filter_var($email, FILTER_VALIDATE_EMAIL));
    }
    
    private function GetMXrecords(string $hostname)
    {
        $mxhosts = [];
        $mxweights = [];
        if (getmxrr($hostname, $mxhosts, $mxweights))
            array_multisort($mxweights, $mxhosts);
        
        $mxhosts[] = $hostname;
        
        return $mxhosts;
    }
    
    private function ParseEmail(&$email)
    {
        return sscanf($email, "%[^@]@%s");
    }
    
    private function SockQuery(&$fp, $query)
    {
        stream_socket_sendto($fp, $query . "\r\n");
        
        return $this->SockGetResponseCode($fp);
    }
    
    private function SockGetResponseCode(&$fp)
    {
        $reply = stream_get_line($fp, 1);
        $status = stream_get_meta_data($fp);
        if ($status['unread_bytes'] > 0)
            $reply .= stream_get_line($fp, $status['unread_bytes'], "\r\n");
        
        if (strlen($reply) >= 3)
            return substr($reply, 0, 3);
        
        return '';
    }
}