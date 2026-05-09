<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIPlannerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setEnvValue('GEMINI_API_KEY', 'test-key');
        $this->setEnvValue('GEMINI_MODEL_FALLBACKS', 'model-a,model-b');
        $this->setEnvValue('PEXELS_API_KEY', 'pexels-test-key');

        $this->seedPrompt();
    }

    protected function tearDown(): void
    {
        $this->setEnvValue('GEMINI_API_KEY', null);
        $this->setEnvValue('GEMINI_MODEL_FALLBACKS', null);
        $this->setEnvValue('PEXELS_API_KEY', null);

        parent::tearDown();
    }

    public function test_ai_success_returns_plan_source_ai(): void
    {
        $aiItinerary = [
            [
                'tieuDe' => 'Ngay 1: Kham pha Da Nang',
                'thoiGian' => 'Thu hai',
                'danhSachHoatDong' => [
                    [
                        'buoi' => 'BUOI SANG',
                        'iconClass' => 'icon--morning',
                        'icon' => 'fas fa-sun',
                        'tieuDe' => 'Bien My Khe',
                        'moTa' => 'Tam bien va thu gian',
                        'hinhanh' => '',
                        'co_trong_db' => false,
                        'gia' => 'Mien phi',
                        'thoiLuong' => '3 gio',
                    ],
                    [
                        'buoi' => 'BUOI CHIEU',
                        'iconClass' => 'icon--afternoon',
                        'icon' => 'fas fa-cloud-sun',
                        'tieuDe' => 'Ngu Hanh Son',
                        'moTa' => 'Tham quan danh thang',
                        'hinhanh' => '',
                        'co_trong_db' => false,
                        'gia' => '50.000 VND',
                        'thoiLuong' => '3 gio',
                    ],
                    [
                        'buoi' => 'BUOI TOI',
                        'iconClass' => 'icon--evening',
                        'icon' => 'fas fa-moon',
                        'tieuDe' => 'Cau Rong',
                        'moTa' => 'Check-in buoi toi',
                        'hinhanh' => '',
                        'co_trong_db' => false,
                        'gia' => 'Mien phi',
                        'thoiLuong' => '2 gio',
                    ],
                ],
            ],
        ];

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => json_encode($aiItinerary, JSON_UNESCAPED_UNICODE)],
                            ],
                        ],
                    ],
                ],
            ], 200),
            'https://api.pexels.com/*' => Http::response([
                'photos' => [
                    ['src' => ['landscape' => 'https://images.example.com/pexels-cover.jpg']],
                ],
            ], 200),
        ]);

        $response = $this->postJson('/api/khach-hang/ke-hoach-ai', [
            'diemDen' => 'Da Nang',
            'soNgay' => 1,
            'nganSach' => 'Tieu chuan',
            'soThich' => ['am thuc'],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.meta.planSource', 'ai')
            ->assertJsonStructure([
                'success',
                'data' => [
                    'tieuDe',
                    'thoiGian',
                    'nganSach',
                    'hinhAnh',
                    'lichTrinh',
                    'tongChiPhi',
                    'meta' => ['planSource'],
                ],
            ]);
    }

    public function test_ai_429_all_models_returns_fallback_plan(): void
    {
        $this->seedDiaDiem('0', 'Bai bien Son Tra', 'Da Nang', 0, 'https://images.example.com/son-tra.jpg');
        $this->seedDiaDiem('1', 'Cau Tinh Yeu', 'Da Nang', 0, 'https://images.example.com/cau-tinh-yeu.jpg');

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'error' => [
                    'message' => 'Quota exceeded',
                ],
            ], 429),
        ]);

        $response = $this->postJson('/api/khach-hang/ke-hoach-ai', [
            'diemDen' => 'Da Nang',
            'soNgay' => 2,
            'nganSach' => 'Tiet kiem',
            'soThich' => ['nghi duong'],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.meta.planSource', 'fallback')
            ->assertJsonStructure([
                'success',
                'data' => [
                    'lichTrinh',
                    'meta' => ['planSource', 'notice'],
                ],
            ]);

        $lichTrinh = $response->json('data.lichTrinh');
        $this->assertIsArray($lichTrinh);
        $this->assertCount(2, $lichTrinh);
        $this->assertCount(3, $lichTrinh[0]['danhSachHoatDong']);
    }

    public function test_missing_api_key_returns_structured_ai_auth_error_json(): void
    {
        $this->setEnvValue('GEMINI_API_KEY', null);

        $response = $this->postJson('/api/khach-hang/ke-hoach-ai', [
            'diemDen' => 'Da Nang',
            'soNgay' => 1,
            'nganSach' => 'Tieu chuan',
            'soThich' => ['am thuc'],
        ]);

        $response
            ->assertStatus(500)
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 'AI_AUTH')
            ->assertJsonPath('retryable', false)
            ->assertJsonStructure([
                'success',
                'message',
                'code',
                'retryable',
            ]);

        $this->assertStringContainsString('application/json', (string) $response->headers->get('content-type'));
    }

    public function test_ai_generate_accepts_selected_locations_and_trip_description(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => function ($request) {
                $payload = $request->data();
                $prompt = data_get($payload, 'contents.0.parts.0.text', '');

                $this->assertStringContainsString('Hon Thom', $prompt);
                $this->assertStringContainsString('Muon di an nhieu do bien', $prompt);

                return Http::response([
                    'candidates' => [
                        [
                            'content' => [
                                'parts' => [
                                    [
                                        'text' => json_encode([
                                            [
                                                'tieuDe' => 'Ngay 1: Phu Quoc',
                                                'thoiGian' => 'Thu hai',
                                                'danhSachHoatDong' => [
                                                    [
                                                        'buoi' => 'BUOI SANG',
                                                        'iconClass' => 'icon--morning',
                                                        'icon' => 'fas fa-sun',
                                                        'tieuDe' => 'Hon Thom',
                                                        'moTa' => 'Tam bien',
                                                        'hinhanh' => '',
                                                        'co_trong_db' => false,
                                                        'gia' => '100.000 VND',
                                                        'thoiLuong' => '3 gio',
                                                    ],
                                                ],
                                            ],
                                        ], JSON_UNESCAPED_UNICODE),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ], 200);
            },
            'https://api.pexels.com/*' => Http::response(['photos' => []], 200),
        ]);

        $response = $this->postJson('/api/khach-hang/ke-hoach-ai', [
            'diemDen' => 'Phu Quoc',
            'soNgay' => 1,
            'nganSach' => 'Tieu chuan',
            'soThich' => ['am thuc'],
            'selectedLocations' => ['Hon Thom'],
            'moTaChuyenDi' => 'Muon di an nhieu do bien',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.meta.planSource', 'ai');
    }

    public function test_ai_suggest_locations_returns_suggested_places(): void
    {
        $this->seedDiaDiem('10', 'Cho dem Phu Quoc', 'Phu Quoc', 0, 'https://images.example.com/phu-quoc.jpg');

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        [
                                            'ten_dia_diem' => 'Hon Thom',
                                            'mo_ta_ngan' => 'Cap treo va bien dep',
                                            'dia_chi' => 'Nam dao Phu Quoc',
                                            'hinhanh' => 'https://images.example.com/hon-thom.jpg',
                                        ],
                                    ], JSON_UNESCAPED_UNICODE),
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson('/api/khach-hang/ke-hoach-ai/de-xuat-dia-diem', [
            'diem_den' => 'Phu Quoc',
            'so_ngay' => 2,
            'ngan_sach' => 'Tieu chuan',
            'so_thich' => ['bien dao'],
            'mo_ta_chuyen_di' => 'Muon di bien va an hai san',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.ten_dia_diem', 'Hon Thom')
            ->assertJsonPath('data.0.hinhanh', 'https://images.example.com/hon-thom.jpg');
    }

    public function test_ai_suggest_locations_enriches_missing_images_with_pexels(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        [
                                            'ten_dia_diem' => 'Hồ Hoàn Kiếm',
                                            'mo_ta_ngan' => 'Đi dạo và ngắm cảnh trung tâm Hà Nội',
                                            'dia_chi' => 'Quận Hoàn Kiếm, Hà Nội',
                                            'hinhanh' => '',
                                        ],
                                    ], JSON_UNESCAPED_UNICODE),
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
            'https://api.pexels.com/*' => function ($request) {
                $this->assertStringContainsString('Hồ Hoàn Kiếm', (string) $request['query']);

                return Http::response([
                    'photos' => [
                        ['src' => ['landscape' => 'https://images.example.com/ho-hoan-kiem.jpg']],
                    ],
                ], 200);
            },
        ]);

        $response = $this->postJson('/api/khach-hang/ke-hoach-ai/de-xuat-dia-diem', [
            'diem_den' => 'Hà Nội',
            'so_ngay' => 2,
            'ngan_sach' => 'Tiêu chuẩn',
            'so_thich' => ['văn hóa'],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.ten_dia_diem', 'Hồ Hoàn Kiếm')
            ->assertJsonPath('data.0.hinhanh', 'https://images.example.com/ho-hoan-kiem.jpg');
    }

    private function seedPrompt(): void
    {
        DB::table('cau_hinh_ais')->insert([
            'loai' => 'prompt_mac_dinh',
            'noi_dung' => 'Lap ke hoach cho {diemDen} trong {soNgay} ngay, ngan sach {nganSach}, so thich {soThich}, du lieu {dbJson}. Tra ve json.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedDiaDiem(string $maDiaDiem, string $tenDiaDiem, string $diaChi, float $gia, string $hinhAnh): void
    {
        DB::table('dia_diem')->insert([
            'ma_dia_diem' => $maDiaDiem,
            'ten_dia_diem' => $tenDiaDiem,
            'loai' => 1,
            'dia_chi' => $diaChi,
            'sdt' => null,
            'kinh_do' => 108.2022,
            'vi_do' => 16.0544,
            'gio_mo_cua' => null,
            'gio_dong_cua' => null,
            'gia_giao_dong' => $gia,
            'hinh_anh' => $hinhAnh,
            'mo_ta' => 'Mo ta ngắn',
            'thoi_gian_tham_quan' => '2 gio',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function setEnvValue(string $key, ?string $value): void
    {
        if ($value === null) {
            putenv($key);
            unset($_ENV[$key], $_SERVER[$key]);

            return;
        }

        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}
