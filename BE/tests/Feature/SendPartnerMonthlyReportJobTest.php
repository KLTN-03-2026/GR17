<?php

namespace Tests\Feature;

use App\Jobs\SendPartnerMonthlyReportJob;
use App\Mail\PartnerMonthlyReportMail;
use App\Models\BaoCaoDoiTacThang;
use App\Models\DoiTac;
use App\Services\PartnerMonthlyReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class SendPartnerMonthlyReportJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_queues_monthly_report_email_and_marks_log_as_sent(): void
    {
        Mail::fake();

        $doiTac = DoiTac::create([
            'ma_doi_tac' => '4001',
            'ten_doi_tac' => 'Doi Tac Mail',
            'ten_nguoi_dai_dien' => 'Nguoi Dai Dien 4',
            'email' => 'mail@example.com',
            'mat_khau' => Hash::make('123456'),
            'is_block' => false,
            'trang_thai_duyet' => 'approved',
        ]);

        BaoCaoDoiTacThang::create([
            'ma_doi_tac' => $doiTac->ma_doi_tac,
            'thang_bao_cao' => '2026-03-01',
            'trang_thai' => 'queued',
            'so_lan_thu' => 0,
        ]);

        $job = new SendPartnerMonthlyReportJob($doiTac->ma_doi_tac, '2026-03');
        $job->handle(app(PartnerMonthlyReportService::class));

        Mail::assertQueued(PartnerMonthlyReportMail::class, function (PartnerMonthlyReportMail $mail) use ($doiTac) {
            return $mail->hasTo($doiTac->email);
        });

        $this->assertDatabaseHas('bao_cao_doi_tac_thang', [
            'ma_doi_tac' => $doiTac->ma_doi_tac,
            'thang_bao_cao' => '2026-03-01',
            'trang_thai' => 'sent',
        ]);
    }

    public function test_job_marks_log_failed_when_exception_happens(): void
    {
        $doiTac = DoiTac::create([
            'ma_doi_tac' => '4002',
            'ten_doi_tac' => 'Doi Tac Error',
            'ten_nguoi_dai_dien' => 'Nguoi Dai Dien 5',
            'email' => 'error@example.com',
            'mat_khau' => Hash::make('123456'),
            'is_block' => false,
            'trang_thai_duyet' => 'approved',
        ]);

        BaoCaoDoiTacThang::create([
            'ma_doi_tac' => $doiTac->ma_doi_tac,
            'thang_bao_cao' => '2026-03-01',
            'trang_thai' => 'queued',
            'so_lan_thu' => 0,
        ]);

        $service = $this->createMock(PartnerMonthlyReportService::class);
        $service
            ->method('buildForMonth')
            ->willThrowException(new RuntimeException('Service unavailable'));

        $job = new SendPartnerMonthlyReportJob($doiTac->ma_doi_tac, '2026-03');

        try {
            $job->handle($service);
            $this->fail('Expected RuntimeException was not thrown.');
        } catch (RuntimeException $exception) {
            $job->failed($exception);
        }

        $this->assertSame([60, 300, 900], $job->backoff());

        $this->assertDatabaseHas('bao_cao_doi_tac_thang', [
            'ma_doi_tac' => $doiTac->ma_doi_tac,
            'thang_bao_cao' => '2026-03-01',
            'trang_thai' => 'failed',
        ]);
    }
}
