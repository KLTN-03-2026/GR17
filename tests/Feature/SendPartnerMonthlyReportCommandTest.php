<?php

namespace Tests\Feature;

use App\Jobs\SendPartnerMonthlyReportJob;
use App\Models\BaoCaoDoiTacThang;
use App\Models\DoiTac;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SendPartnerMonthlyReportCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_dispatches_jobs_only_for_approved_unblocked_partners(): void
    {
        Bus::fake();

        $approved = DoiTac::create([
            'ma_doi_tac' => '3001',
            'ten_doi_tac' => 'Doi Tac Approved',
            'ten_nguoi_dai_dien' => 'Nguoi Dai Dien 1',
            'email' => 'approved@example.com',
            'mat_khau' => Hash::make('123456'),
            'is_block' => false,
            'trang_thai_duyet' => 'approved',
        ]);

        DoiTac::create([
            'ma_doi_tac' => '3002',
            'ten_doi_tac' => 'Doi Tac Pending',
            'ten_nguoi_dai_dien' => 'Nguoi Dai Dien 2',
            'email' => 'pending@example.com',
            'mat_khau' => Hash::make('123456'),
            'is_block' => false,
            'trang_thai_duyet' => 'pending',
        ]);

        DoiTac::create([
            'ma_doi_tac' => '3003',
            'ten_doi_tac' => 'Doi Tac Blocked',
            'ten_nguoi_dai_dien' => 'Nguoi Dai Dien 3',
            'email' => 'blocked@example.com',
            'mat_khau' => Hash::make('123456'),
            'is_block' => true,
            'trang_thai_duyet' => 'approved',
        ]);

        BaoCaoDoiTacThang::create([
            'ma_doi_tac' => $approved->ma_doi_tac,
            'thang_bao_cao' => '2026-03-01',
            'trang_thai' => 'sent',
            'so_lan_thu' => 1,
            'sent_at' => now(),
        ]);

        $this->artisan('doi-tac:email-bao-cao-thang', [
            '--month' => '2026-03',
        ])->assertExitCode(0);

        Bus::assertNotDispatched(SendPartnerMonthlyReportJob::class);
    }

    public function test_command_dispatches_job_for_failed_report_log(): void
    {
        Bus::fake();

        $doiTac = DoiTac::create([
            'ma_doi_tac' => '3004',
            'ten_doi_tac' => 'Doi Tac Failed',
            'ten_nguoi_dai_dien' => 'Nguoi Dai Dien 4',
            'email' => 'failed@example.com',
            'mat_khau' => Hash::make('123456'),
            'is_block' => false,
            'trang_thai_duyet' => 'approved',
        ]);

        BaoCaoDoiTacThang::create([
            'ma_doi_tac' => $doiTac->ma_doi_tac,
            'thang_bao_cao' => '2026-03-01',
            'trang_thai' => 'failed',
            'so_lan_thu' => 3,
            'error_message' => 'Mail server timeout',
        ]);

        $this->artisan('doi-tac:email-bao-cao-thang', [
            '--month' => '2026-03',
        ])->assertExitCode(0);

        Bus::assertDispatched(SendPartnerMonthlyReportJob::class, function (SendPartnerMonthlyReportJob $job) use ($doiTac) {
            return $job->maDoiTac === $doiTac->ma_doi_tac && $job->reportMonth === '2026-03';
        });
    }
}
