<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = 'company@email.com';
        $subject = 'Nova Mensagem da Contato';

        return $this->view('email.contactMessage')
                    ->from($address, $this->data['name'])
                    ->cc($address, $this->data['name'])
                    ->bcc($address, $this->data['name'])
                    ->replyTo($address, $this->data['name'])
                    ->subject($subject)
                    ->with([
                        '_name'      => $this->data['name'],
                        '_message'   => $this->data['message'],
                        '_ourEmail'  => $address,
                        '_path_file' => trim( $this->data['file_path'], '/')
                    ]);
    }
}
