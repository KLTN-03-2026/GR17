<?php

namespace App\Console\Commands;

use App\Jobs\SendPartnerMonthlyReportJob;
use App\Models\BaoCaoDoiTacThang;
use App\Models\DoiTac;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendPartnerMonthlyReportCommand extends Command
{
    protected $signature = 'doi-tac:email-bao-cao-thang
                            {--month= : Thang bao cao dang Y-m (vi du: 2026-03)}
                            {--force : Gui lai bao cao du da sent}';

    protected $description = 'Gui email bao cao doanh thu cuoi thang cho doi tac';

    public function handle(): int
    {
        $reportMonth = $this->resolveReportMonth();
        $thangBaoCao = Carbon::createFromFormat('Y-m', $reportMonth, 'Asia/Ho_Chi_Minh')
            ->startOfMonth()
            ->toDateString();

        $partners = DoiTac::query()
            ->where('trang_thai_duyet', 'approved')
            ->where('is_block', false)
            ->get(['ma_doi_tac', 'email', 'ten_doi_tac']);

        if ($partners->isEmpty()) {
            $this->info('Khong co doi tac hop le de gui bao cao.');
            return self::SUCCESS;
        }

        $dispatched = 0;
        $skipped = 0;
        $force = (bool) $this->option('force');

        foreach ($partners as $partner) {
            if (empty($partner->email)) {
                $skipped++;
                continue;
            }

            $log = BaoCaoDoiTacThang::query()->firstOrCreate(
                [
                    'ma_doi_tac' => $partner->ma_doi_tac,
                    'thang_bao_cao' => $thangBaoCao,
                ],
                [
                    'trang_thai' => 'queued',
                    'so_lan_thu' => 0,
                ]
            );

            if (! $force && in_array($log->trang_thai, ['sent', 'queued', 'processing'], true)) {
                $skipped++;
                continue;
            }

            if ($force && $log->trang_thai === 'sent') {
                $log->update([
                    'trang_thai' => 'queued',
                    'sent_at' => null,
                    'error_message' => null,
                ]);
            }

            SendPartnerMonthlyReportJob::dispatch($partner->ma_doi_tac, $reportMonth)->onQueue('emails');
            $dispatched++;
        }

        $this->info("Thang bao cao: {$reportMonth}");
        $this->info("Da dispatch: {$dispatched} job");
        $this->info("Bo qua: {$skipped} doi tac");

        return self::SUCCESS;
    }

    private function resolveReportMonth(): string
    {
        $month = (string) $this->option('month');
        if ($month !== '') {
            return Carbon::createFromFormat('Y-m', $month, 'Asia/Ho_Chi_Minh')->format('Y-m');
        }

        return now('Asia/Ho_Chi_Minh')->subMonthNoOverflow()->format('Y-m');
    }
}
