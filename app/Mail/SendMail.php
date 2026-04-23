<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
	use Queueable, SerializesModels;

	public $from_email;
	public $site_title;
	public $subject;
	public $message;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($email_from, $subject, $message, $fromName = null)
	{
		$basic = basicControl();
		$this->from_email = $email_from;
		$this->site_title = ($fromName) ?: $basic->site_title;
		$this->subject = $subject;
		$this->message = $message;
	}

	public function envelope(): Envelope
	{
		return new Envelope(
			from: new Address($this->from_email, $this->site_title),
			subject: $this->subject,
		);
	}

	public function content(): Content
	{
		return new Content(
			view: 'layouts.mail',
			with: [
				'msg' => $this->message,
			],
		);
	}
}
