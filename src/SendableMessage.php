<?php

namespace Mission4\SimpleGmail;

use Swift_Message;
use Swift_Attachment;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Http\UploadedFile;

class SendableMessage
{
    protected $client;
    protected $service;

    public function __construct($client)
    {
        $this->client = $client;
        $this->service = new Google_Service_Gmail($this->client);
        $this->message = new Swift_Message;

        return $this;
    }

    public function to($to = [])
    {
        $this->message->setTo($to);
        return $this;
    }

    public function cc($cc = [])
    {
        $this->message->setCc($cc);
        return $this;
    }

    public function attachFile(UploadedFile $file)
    {
        $attachment = (new Swift_Attachment())
            ->setFilename($file->getClientOriginalName())
            ->setContentType($file->getClientMimeType())
            ->setBody($file->get());

        $this->message->attach($attachment);
        return $this;
    }

    public function bcc($bcc = [])
    {
        $this->message->setBcc($bcc);
        return $this;
    }

    public function body($body = "")
    {
        $this->message->setBody($body, 'text/html');
        return $this;
    }

    public function subject($subject = "")
    {
        $this->message->setSubject($subject);
        return $this;
    }

    public function send()
    {
        $gm_message = new Google_Service_Gmail_Message();
        $gm_message->setRaw(rtrim(strtr(base64_encode($this->message->toString()), '+/', '-_'), '='));
        $sent = $this->service->users_messages->send('me', $gm_message);
        if ($sent) {
            $msg = $this->service->users_messages->get('me', $sent->id);
            // Collect headers
            $headers = collect($msg->getPayload()->headers);
            return [
                'id' => $sent->id,
            ];
        }
    }
}
