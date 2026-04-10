<?php

namespace App\Jobs;

use App\Mail\PartnerMonthlyReportMail;
use App\Models\BaoCaoDoiTacThang;
use App\Models\DoiTac;
use App\Services\PartnerMonthlyReportService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendPartnerMonthlyReportJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $maDoiTac,
        public string $reportMonth,
    ) {
        $this->onQueue('emails');
    }

    public function uniqueId(): string
    {
        return $this->maDoiTac.'-'.$this->reportMonth;
    }

    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function handle(PartnerMonthlyReportService $reportService): void
    {
        $thangBaoCao = Carbon::createFromFormat('Y-m', $this->reportMonth, 'Asia/Ho_Chi_Minh')
            ->startOfMonth()
            ->toDateString();

        $shouldSend = DB::transaction(function () use ($thangBaoCao): bool {
            $log = BaoCaoDoiTacThang::query()
                ->lockForUpdate()
                ->firstOrCreate(
                    [
                        'ma_doi_tac' => $this->maDoiTac,
                        'thang_bao_cao' => $thangBaoCao,
                    ],
                    [
                        'trang_thai' => 'queued',
                        'so_lan_thu' => 0,
                    ]
                );

            if ($log->trang_thai === 'sent') {
                return false;
            }

            $log->update([
                'trang_thai' => 'processing',
                'so_lan_thu' => $log->so_lan_thu + 1,
                'error_message' => null,
            ]);

            return true;
        });

        if (! $shouldSend) {
            return;
        }

        $doiTac = DoiTac::query()->where('ma_doi_tac', $this->maDoiTac)->first();
        if (! $doiTac || empty($doiTac->email)) {
            throw new \RuntimeException('Khong tim thay email doi tac de gui bao cao.');
        }

        $reportData = $reportService->buildForMonth($this->maDoiTac, $this->reportMonth);

        Mail::to($doiTac->email)->queue(new PartnerMonthlyReportMail($doiTac, $reportData));

        BaoCaoDoiTacThang::query()
            ->where('ma_doi_tac', $this->maDoiTac)
            ->whereDate('thang_bao_cao', $thangBaoCao)
            ->update([
                'trang_thai' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);
    }

    public function failed(Throwable $exception): void
    {
        $thangBaoCao = Carbon::createFromFormat('Y-m', $this->reportMonth, 'Asia/Ho_Chi_Minh')
            ->startOfMonth()
            ->toDateString();

        BaoCaoDoiTacThang::query()
            ->where('ma_doi_tac', $this->maDoiTac)
            ->whereDate('thang_bao_cao', $thangBaoCao)
            ->update([
                'trang_thai' => 'failed',
                'error_message' => mb_substr($exception->getMessage(), 0, 1000),
            ]);
    }
}
