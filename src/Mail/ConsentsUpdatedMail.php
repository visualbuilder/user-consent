<?php

namespace Visualbuilder\FilamentUserConsent\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConsentsUpdatedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $consentOptions = $this->user->activeConsents()->get();

        $content = view('vendor.user-consent.mails.accept-notification', ['consentOptions' => $consentOptions])->render();

        return $this->from(
            config('mail.from.address'),
            config('mail.from.name')
        )->view(config('filament-user-consent.email-template'))
            ->subject(__('Your consent'))
            ->to($this->user->email)
            ->with([
                'content' => $content,
                'preHeaderText' => __('Your Consent Agreement'),
                'title' => __('Your Consent Agreement'),
            ]);
    }
}
