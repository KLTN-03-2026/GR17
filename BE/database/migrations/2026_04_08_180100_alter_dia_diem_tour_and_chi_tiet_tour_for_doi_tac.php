<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $this->syncDiaDiemSchema();
        $this->syncTourSchema();
        $this->syncChiTietTourSchema();
        $this->backfillModerationDefaults();
        $this->backfillNormalizedDiaDiem();
    }

    public function down(): void
    {
        // Migration bu an toan: khong rollback schema de tranh mat du lieu hien co.
    }

    private function syncDiaDiemSchema(): void
    {
        $columnsToAdd = [];

        if (!$this->hasColumn('dia_diem', 'ma_doi_tac_tao')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('ma_doi_tac_tao', 10)->nullable()->after('ma_dia_diem');
            };
        }
        if (!$this->hasColumn('dia_diem', 'nguon_tao')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('nguon_tao', 30)->default('admin')->after('thoi_gian_tham_quan');
            };
        }
        if (!$this->hasColumn('dia_diem', 'trang_thai_duyet')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('trang_thai_duyet', 30)->default('approved')->after('nguon_tao');
            };
        }
        if (!$this->hasColumn('dia_diem', 'ly_do_tu_choi')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->text('ly_do_tu_choi')->nullable()->after('trang_thai_duyet');
            };
        }
        if (!$this->hasColumn('dia_diem', 'ten_dia_diem_normalized')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('ten_dia_diem_normalized')->default('')->after('ten_dia_diem');
            };
        }
        if (!$this->hasColumn('dia_diem', 'dia_chi_normalized')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('dia_chi_normalized')->default('')->after('dia_chi');
            };
        }

        if (!empty($columnsToAdd)) {
            Schema::table('dia_diem', function (Blueprint $table) use ($columnsToAdd): void {
                foreach ($columnsToAdd as $callback) {
                    $callback($table);
                }
            });
        }

        Schema::table('dia_diem', function (Blueprint $table): void {
            if (!$this->hasIndex('dia_diem', 'dia_diem_trang_thai_loai_idx')) {
                $table->index(['trang_thai_duyet', 'loai'], 'dia_diem_trang_thai_loai_idx');
            }
            if (!$this->hasIndex('dia_diem', 'dia_diem_normalized_lookup_idx')) {
                $table->index(['ten_dia_diem_normalized', 'dia_chi_normalized'], 'dia_diem_normalized_lookup_idx');
            }
            if (!$this->hasIndex('dia_diem', 'dia_diem_ma_doi_tac_tao_idx')) {
                $table->index('ma_doi_tac_tao', 'dia_diem_ma_doi_tac_tao_idx');
            }
        });
    }

    private function syncTourSchema(): void
    {
        $columnsToAdd = [];

        if (!$this->hasColumn('tours', 'ma_doi_tac')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('ma_doi_tac', 10)->nullable()->after('ma_tour');
            };
        }
        if (!$this->hasColumn('tours', 'nguon_tao')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('nguon_tao', 30)->default('admin')->after('ma_tag');
            };
        }
        if (!$this->hasColumn('tours', 'trang_thai_duyet')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->string('trang_thai_duyet', 30)->default('approved')->after('nguon_tao');
            };
        }
        if (!$this->hasColumn('tours', 'trang_thai_hien_thi')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->boolean('trang_thai_hien_thi')->default(true)->after('trang_thai_duyet');
            };
        }
        if (!$this->hasColumn('tours', 'ly_do_tu_choi')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->text('ly_do_tu_choi')->nullable()->after('trang_thai_hien_thi');
            };
        }

        if (!empty($columnsToAdd)) {
            Schema::table('tours', function (Blueprint $table) use ($columnsToAdd): void {
                foreach ($columnsToAdd as $callback) {
                    $callback($table);
                }
            });
        }

        Schema::table('tours', function (Blueprint $table): void {
            if (!$this->hasIndex('tours', 'tour_public_status_idx')) {
                $table->index(['trang_thai_duyet', 'trang_thai_hien_thi'], 'tour_public_status_idx');
            }
            if (!$this->hasIndex('tours', 'tour_ma_doi_tac_idx')) {
                $table->index('ma_doi_tac', 'tour_ma_doi_tac_idx');
            }
        });
    }

    private function syncChiTietTourSchema(): void
    {
        $columnsToAdd = [];

        if (!$this->hasColumn('chi_tiet_tours', 'thu_tu_hanh_trinh')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->integer('thu_tu_hanh_trinh')->default(1)->after('ma_dia_diem');
            };
        }
        if (!$this->hasColumn('chi_tiet_tours', 'ghi_chu_hanh_trinh')) {
            $columnsToAdd[] = static function (Blueprint $table): void {
                $table->text('ghi_chu_hanh_trinh')->nullable()->after('thu_tu_hanh_trinh');
            };
        }

        if (!empty($columnsToAdd)) {
            Schema::table('chi_tiet_tours', function (Blueprint $table) use ($columnsToAdd): void {
                foreach ($columnsToAdd as $callback) {
                    $callback($table);
                }
            });
        }

        if (!$this->hasIndex('chi_tiet_tours', 'chi_tiet_tour_unique_tour_location')) {
            $this->removeDuplicateChiTietTourRows();

            Schema::table('chi_tiet_tours', function (Blueprint $table): void {
                $table->unique(['ma_tour', 'ma_dia_diem'], 'chi_tiet_tour_unique_tour_location');
            });
        }

        if (!$this->hasIndex('chi_tiet_tours', 'chi_tiet_tour_order_idx')) {
            Schema::table('chi_tiet_tours', function (Blueprint $table): void {
                $table->index(['ma_tour', 'thu_tu_hanh_trinh'], 'chi_tiet_tour_order_idx');
            });
        }

        DB::table('chi_tiet_tours')
            ->whereNull('thu_tu_hanh_trinh')
            ->update(['thu_tu_hanh_trinh' => 1]);
    }

    private function removeDuplicateChiTietTourRows(): void
    {
        $duplicates = DB::table('chi_tiet_tours')
            ->select('ma_tour', 'ma_dia_diem', DB::raw('COUNT(*) as total'))
            ->whereNotNull('ma_tour')
            ->whereNotNull('ma_dia_diem')
            ->groupBy('ma_tour', 'ma_dia_diem')
            ->having('total', '>', 1)
            ->get();

        foreach ($duplicates as $row) {
            $ids = DB::table('chi_tiet_tours')
                ->where('ma_tour', $row->ma_tour)
                ->where('ma_dia_diem', $row->ma_dia_diem)
                ->orderBy('ma_chi_tiet_tour')
                ->pluck('ma_chi_tiet_tour')
                ->values();

            if ($ids->count() <= 1) {
                continue;
            }

            $deleteIds = $ids->slice(1)->all();
            DB::table('chi_tiet_tours')
                ->whereIn('ma_chi_tiet_tour', $deleteIds)
                ->delete();
        }
    }

    private function backfillModerationDefaults(): void
    {
        if ($this->hasColumn('dia_diem', 'nguon_tao')) {
            DB::table('dia_diem')->whereNull('nguon_tao')->update(['nguon_tao' => 'admin']);
            DB::table('dia_diem')->where('nguon_tao', '')->update(['nguon_tao' => 'admin']);
        }
        if ($this->hasColumn('dia_diem', 'trang_thai_duyet')) {
            DB::table('dia_diem')->whereNull('trang_thai_duyet')->update(['trang_thai_duyet' => 'approved']);
            DB::table('dia_diem')->where('trang_thai_duyet', '')->update(['trang_thai_duyet' => 'approved']);
        }

        if ($this->hasColumn('tours', 'nguon_tao')) {
            DB::table('tours')->whereNull('nguon_tao')->update(['nguon_tao' => 'admin']);
            DB::table('tours')->where('nguon_tao', '')->update(['nguon_tao' => 'admin']);
        }
        if ($this->hasColumn('tours', 'trang_thai_duyet')) {
            DB::table('tours')->whereNull('trang_thai_duyet')->update(['trang_thai_duyet' => 'approved']);
            DB::table('tours')->where('trang_thai_duyet', '')->update(['trang_thai_duyet' => 'approved']);
        }
        if ($this->hasColumn('tours', 'trang_thai_hien_thi')) {
            DB::table('tours')->whereNull('trang_thai_hien_thi')->update(['trang_thai_hien_thi' => true]);
        }
    }

    private function backfillNormalizedDiaDiem(): void
    {
        if (
            !$this->hasColumn('dia_diem', 'ten_dia_diem_normalized')
            || !$this->hasColumn('dia_diem', 'dia_chi_normalized')
        ) {
            return;
        }

        DB::table('dia_diem')
            ->select('ma_dia_diem', 'ten_dia_diem', 'dia_chi')
            ->orderBy('ma_dia_diem')
            ->get()
            ->each(function ($diaDiem): void {
                DB::table('dia_diem')
                    ->where('ma_dia_diem', $diaDiem->ma_dia_diem)
                    ->update([
                        'ten_dia_diem_normalized' => $this->normalizeValue($diaDiem->ten_dia_diem),
                        'dia_chi_normalized' => $this->normalizeValue($diaDiem->dia_chi),
                    ]);
            });
    }

    private function normalizeValue(?string $value): string
    {
        $value = trim((string) $value);
        $value = mb_strtolower($value, 'UTF-8');
        $value = Str::ascii($value);
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value) ?? '';
        $value = preg_replace('/\s+/', ' ', $value) ?? '';

        return trim($value);
    }

    private function hasColumn(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            $database = DB::getDatabaseName();
            $result = DB::selectOne(
                'SELECT COUNT(1) AS aggregate FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
                [$database, $table, $indexName]
            );

            return ((int) ($result->aggregate ?? 0)) > 0;
        }

        if ($driver === 'sqlite') {
            $rows = DB::select(sprintf("PRAGMA index_list('%s')", $table));
            foreach ($rows as $row) {
                if (($row->name ?? null) === $indexName) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }
};
