<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->syncHoaDonSchema();
        $this->createGiaoDichQrTable();
        $this->backfillHoaDonPaymentFields();
    }

    public function down(): void
    {
        // Giữ dữ liệu checkout/QR để tránh mất thông tin thanh toán.
    }

    private function syncHoaDonSchema(): void
    {
        if (!Schema::hasTable('hoa_don')) {
            return;
        }

        $columnsToAdd = [];

        if (!Schema::hasColumn('hoa_don', 'ma_khach_hang_dat')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('ma_khach_hang_dat', 10)->nullable()->after('ma_nhom');
            };
        }

        if (!Schema::hasColumn('hoa_don', 'ma_thoi_gian_tour')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('ma_thoi_gian_tour', 10)->nullable()->after('ma_doi_tuong');
            };
        }

        if (!Schema::hasColumn('hoa_don', 'payment_method')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('payment_method', 50)->nullable()->after('trang_thai_thanh_toan');
            };
        }

        if (!Schema::hasColumn('hoa_don', 'payment_status')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('payment_status', 20)->nullable()->after('payment_method');
            };
        }

        if (!Schema::hasColumn('hoa_don', 'payment_reference')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('payment_reference', 100)->nullable()->after('payment_status');
            };
        }

        if (!Schema::hasColumn('hoa_don', 'ten_nguoi_dat')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('ten_nguoi_dat', 120)->nullable()->after('payment_reference');
            };
        }

        if (!Schema::hasColumn('hoa_don', 'email_nguoi_dat')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('email_nguoi_dat')->nullable()->after('ten_nguoi_dat');
            };
        }

        if (!Schema::hasColumn('hoa_don', 'so_dien_thoai_nguoi_dat')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('so_dien_thoai_nguoi_dat', 20)->nullable()->after('email_nguoi_dat');
            };
        }

        if (!Schema::hasColumn('hoa_don', 'dia_chi_nguoi_dat')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('dia_chi_nguoi_dat', 255)->nullable()->after('so_dien_thoai_nguoi_dat');
            };
        }

        if (!Schema::hasColumn('hoa_don', 'paid_at')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->timestamp('paid_at')->nullable()->after('ngay_tao');
            };
        }

        if (!empty($columnsToAdd)) {
            Schema::table('hoa_don', function (Blueprint $table) use ($columnsToAdd): void {
                foreach ($columnsToAdd as $callback) {
                    $callback($table);
                }
            });
        }
    }

    private function createGiaoDichQrTable(): void
    {
        if (Schema::hasTable('giao_dich_qr')) {
            return;
        }

        Schema::create('giao_dich_qr', function (Blueprint $table): void {
            $table->string('ma_giao_dich_qr', 26)->primary();
            $table->string('ma_hoa_don', 10);
            $table->string('provider', 20)->default('vietqr');
            $table->decimal('so_tien', 18, 2);
            $table->string('noi_dung_chuyen_khoan', 100);
            $table->text('qr_url');
            $table->json('qr_payload')->nullable();
            $table->string('trang_thai', 20)->default('pending');
            $table->string('webhook_transaction_id', 50)->nullable()->unique();
            $table->string('reference_code', 100)->nullable();
            $table->json('webhook_payload')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('ma_hoa_don')
                ->references('ma_hoa_don')
                ->on('hoa_don')
                ->onDelete('cascade');

            $table->index(['ma_hoa_don', 'trang_thai'], 'giao_dich_qr_invoice_status_idx');
        });
    }

    private function backfillHoaDonPaymentFields(): void
    {
        if (!Schema::hasTable('hoa_don')) {
            return;
        }

        if (Schema::hasColumn('hoa_don', 'payment_status')) {
            DB::table('hoa_don')
                ->whereNull('payment_status')
                ->orderBy('ma_hoa_don')
                ->get(['ma_hoa_don', 'trang_thai_thanh_toan'])
                ->each(function ($hoaDon): void {
                    $paymentStatus = match ((int) ($hoaDon->trang_thai_thanh_toan ?? 0)) {
                        1 => 'paid',
                        2 => 'failed',
                        default => 'pending',
                    };

                    DB::table('hoa_don')
                        ->where('ma_hoa_don', $hoaDon->ma_hoa_don)
                        ->update([
                            'payment_status' => $paymentStatus,
                        ]);
                });
        }

        if (Schema::hasColumn('hoa_don', 'payment_method')) {
            DB::table('hoa_don')
                ->whereNull('payment_method')
                ->update([
                    'payment_method' => 'legacy_manual',
                ]);
        }
    }
};
