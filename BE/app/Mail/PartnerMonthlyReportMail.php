<?php

namespace App\Mail;

use App\Models\DoiTac;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerMonthlyReportMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public DoiTac $doiTac,
        public array $reportData,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bao cao doanh thu thang '.$this->reportData['month'].' - Smart Travel'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.partner-monthly-report'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
