<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\DiaDiem;
use App\Models\HoatDongChiTiet;
use App\Models\KeHoach;
use App\Models\KhachHang;
use App\Models\Nhom;
use App\Services\AITourGuideService;
use App\Models\ThanhVienNhom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OwnershipAndRouteHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_customers_cannot_access_admin_management_routes(): void
    {
        $customer = $this->createCustomer('KH000101', 'customer-101@example.com', '0901100101');

        $this->postJson('/api/chuc-vu', [
            'ten_chuc_vu' => 'Dieu Phoi',
        ])->assertStatus(401);

        $this->postJson('/api/dia-diem', [
            'ten_dia_diem' => 'Địa điểm nội bộ',
            'loai' => 1,
            'dia_chi' => '1 Đường Test, TP.HCM',
            'kinh_do' => 106.6297,
            'vi_do' => 10.8231,
        ])->assertStatus(401);

        Sanctum::actingAs($customer);

        $this->postJson('/api/chuc-vu', [
            'ten_chuc_vu' => 'Dieu Phoi',
        ])->assertStatus(403);

        $this->postJson('/api/dia-diem', [
            'ten_dia_diem' => 'Địa điểm nội bộ',
            'loai' => 1,
            'dia_chi' => '1 Đường Test, TP.HCM',
            'kinh_do' => 106.6297,
            'vi_do' => 10.8231,
        ])->assertStatus(403);
    }

    public function test_guests_cannot_create_groups_or_plans(): void
    {
        $group = $this->createGroup('NHPLAN401', 'Nhóm kế hoạch 401');

        $this->postJson('/api/nhom', [
            'ten_nhom' => 'Nhóm mới',
        ])->assertStatus(401);

        $this->postJson('/api/ke-hoach', [
            'ma_nhom' => $group->Ma_nhom,
            'ten_ke_hoach' => 'Kế hoạch khách',
            'so_nguoi' => 2,
            'ngay_bat_dau' => '2026-05-01',
            'ngay_ket_thuc' => '2026-05-03',
            'ngan_sach_du_kien' => 2500000,
            'trang_thai' => true,
        ])->assertStatus(401);
    }

    public function test_plan_creation_uses_authenticated_customer_membership_instead_of_spoofed_request_customer_id(): void
    {
        $customerA = $this->createCustomer('KH000201', 'customer-201@example.com', '0901200201');
        $customerB = $this->createCustomer('KH000202', 'customer-202@example.com', '0901200202');

        $groupA = $this->createGroup('NHOMAUTH1', 'Nhóm của A');
        $groupB = $this->createGroup('NHOMAUTH2', 'Nhóm của B');

        $this->attachMember('TVAUTH201', $groupA->Ma_nhom, $customerA->Ma_khach_hang, 1);
        $this->attachMember('TVAUTH202', $groupB->Ma_nhom, $customerB->Ma_khach_hang, 1);

        Sanctum::actingAs($customerA);

        $this->postJson('/api/ke-hoach', [
            'ma_khach_hang' => $customerB->Ma_khach_hang,
            'ma_nhom' => $groupB->Ma_nhom,
            'ten_ke_hoach' => 'Kế hoạch giả mạo',
            'so_nguoi' => 2,
            'ngay_bat_dau' => '2026-05-01',
            'ngay_ket_thuc' => '2026-05-03',
            'ngan_sach_du_kien' => 3000000,
            'trang_thai' => true,
        ])->assertStatus(403);

        $this->assertDatabaseMissing('ke_hoach', [
            'ten_ke_hoach' => 'Kế hoạch giả mạo',
        ]);
    }

    public function test_only_group_leader_can_update_or_delete_group(): void
    {
        $leader = $this->createCustomer('KH000301', 'leader-301@example.com', '0901300301');
        $member = $this->createCustomer('KH000302', 'member-302@example.com', '0901300302');

        $group = $this->createGroup('NHOMLEAD1', 'Nhóm trưởng');
        $this->attachMember('TVLEAD301', $group->Ma_nhom, $leader->Ma_khach_hang, 1);
        $this->attachMember('TVLEAD302', $group->Ma_nhom, $member->Ma_khach_hang, 0);

        Sanctum::actingAs($member);

        $this->putJson("/api/nhom/{$group->Ma_nhom}", [
            'ten_nhom' => 'Nhóm bị sửa trái phép',
        ])->assertStatus(403);

        $this->deleteJson("/api/nhom/{$group->Ma_nhom}")
            ->assertStatus(403);

        $this->assertDatabaseHas('nhom', [
            'Ma_nhom' => $group->Ma_nhom,
            'ten_nhom' => 'Nhóm trưởng',
        ]);
    }

    public function test_admin_can_access_hardened_management_routes(): void
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $this->postJson('/api/chuc-vu', [
            'ten_chuc_vu' => 'Dieu Phoi',
        ])->assertCreated();
    }

    public function test_customer_can_materialize_existing_ai_itinerary_for_owned_empty_plan(): void
    {
        $customer = $this->createCustomer('KH000401', 'customer-401@example.com', '0901400401');
        $group = $this->createGroup('NHOMAI401', 'Nhom AI 401');
        $this->attachMember('TVAI401', $group->Ma_nhom, $customer->Ma_khach_hang, 1);

        $location = $this->createLocation('DDAI401', 'Ho Guom', 'Ha Noi', 105.8542, 21.0287);
        $plan = $this->createPlan('PLANAI401', $group->Ma_nhom, [
            'nguon_tao' => 'ai',
            'du_lieu_ai' => [
                'tieuDe' => 'Hanh trinh Ha Noi',
                'lichTrinh' => [
                    [
                        'danhSachHoatDong' => [
                            [
                                'ma_dia_diem' => $location->ma_dia_diem,
                                'tieuDe' => 'Ho Guom',
                                'buoi' => 'BUOI SANG',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        Sanctum::actingAs($customer);

        $this->postJson("/api/ke-hoach/{$plan->ma_ke_hoach}/lich-trinh-ai")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.created', 1);

        $this->assertDatabaseHas('hoat_dong_chi_tiet', [
            'ma_ke_hoach' => $plan->ma_ke_hoach,
            'ma_nhom' => $group->Ma_nhom,
            'ma_dia_diem' => $location->ma_dia_diem,
            'ngay_cu_the' => '2026-05-01',
            'gio_bat_dau' => '08:00:00',
        ]);
    }

    public function test_ai_itinerary_materialization_rejects_existing_activities_without_replace_flag(): void
    {
        $customer = $this->createCustomer('KH000402', 'customer-402@example.com', '0901400402');
        $group = $this->createGroup('NHOMAI402', 'Nhom AI 402');
        $this->attachMember('TVAI402', $group->Ma_nhom, $customer->Ma_khach_hang, 1);

        $location = $this->createLocation('DDAI402', 'Van Mieu', 'Ha Noi', 105.8355, 21.0283);
        $plan = $this->createPlan('PLANAI402', $group->Ma_nhom);
        $this->createActivity('HDAI402', $plan->ma_ke_hoach, $group->Ma_nhom, $location->ma_dia_diem);

        Sanctum::actingAs($customer);

        $this->postJson("/api/ke-hoach/{$plan->ma_ke_hoach}/lich-trinh-ai")
            ->assertStatus(409)
            ->assertJsonPath('success', false);
    }

    public function test_replace_existing_rebuilds_stale_fallback_payload(): void
    {
        $customer = $this->createCustomer('KH000407', 'customer-407@example.com', '0901400407');
        $group = $this->createGroup('NHOMAI407', 'Nhom AI 407');
        $this->attachMember('TVAI407', $group->Ma_nhom, $customer->Ma_khach_hang, 1);

        $north = $this->createLocation('DDNORTH7', 'Hồ Hoàn Kiếm', 'Hà Nội', 105.8522, 21.0285);
        $south = $this->createLocation('DDSOUTH7', 'Trung tam Quan 1', 'Quan 1, TP Ho Chi Minh', 106.699, 10.7798);
        $plan = $this->createPlan('PLANAI407', $group->Ma_nhom, [
            'ten_ke_hoach' => 'Chuyen tham quan mien Bac',
            'mo_ta' => 'Tham quan cac dia diem lich su va canh quan mien Bac',
            'du_lieu_ai' => [
                'generation_mode' => 'fallback',
                'lichTrinh' => [
                    [
                        'danhSachHoatDong' => [
                            ['ma_dia_diem' => $south->ma_dia_diem, 'tieuDe' => $south->ten_dia_diem],
                        ],
                    ],
                ],
            ],
        ]);
        $this->createActivity('HDAI407', $plan->ma_ke_hoach, $group->Ma_nhom, $south->ma_dia_diem);

        $stub = new class extends AITourGuideService {
            public function generatePlan(string $diemDen, int $soNgay, string $nganSach, array $soThich, $diaDiems, $tours, array $selectedLocations = [], string $moTa = '', $selectedTour = null)
            {
                throw new \RuntimeException('503 Service Unavailable');
            }

            public function isRetriableFailure(\Throwable $exception): bool
            {
                return true;
            }
        };

        $this->app->instance(AITourGuideService::class, $stub);

        Sanctum::actingAs($customer);

        $this->postJson("/api/ke-hoach/{$plan->ma_ke_hoach}/lich-trinh-ai", [
            'replace_existing' => true,
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.generation_mode', 'fallback');

        $this->assertDatabaseMissing('hoat_dong_chi_tiet', [
            'ma_ke_hoach' => $plan->ma_ke_hoach,
            'ma_dia_diem' => $south->ma_dia_diem,
        ]);
        $this->assertDatabaseHas('hoat_dong_chi_tiet', [
            'ma_ke_hoach' => $plan->ma_ke_hoach,
            'ma_dia_diem' => $north->ma_dia_diem,
        ]);
    }

    public function test_ai_payload_without_resolvable_locations_degrades_to_internal_fallback(): void
    {
        $customer = $this->createCustomer('KH000408', 'customer-408@example.com', '0901400408');
        $group = $this->createGroup('NHOMAI408', 'Nhom AI 408');
        $this->attachMember('TVAI408', $group->Ma_nhom, $customer->Ma_khach_hang, 1);

        $north = $this->createLocation('DDNORTH8', 'Hồ Hoàn Kiếm', 'Hà Nội', 105.8522, 21.0285);
        $plan = $this->createPlan('PLANAI408', $group->Ma_nhom, [
            'ten_ke_hoach' => 'Chuyen tham quan mien Bac',
            'mo_ta' => 'Tham quan cac dia diem lich su va canh quan mien Bac',
        ]);

        $stub = new class extends AITourGuideService {
            public function generatePlan(string $diemDen, int $soNgay, string $nganSach, array $soThich, $diaDiems, $tours, array $selectedLocations = [], string $moTa = '', $selectedTour = null)
            {
                return [
                    'tieuDe' => 'AI itinerary',
                    'lichTrinh' => [
                        [
                            'danhSachHoatDong' => [
                                ['tieuDe' => 'Unknown place from AI', 'buoi' => 'BUOI SANG'],
                            ],
                        ],
                    ],
                ];
            }
        };

        $this->app->instance(AITourGuideService::class, $stub);

        Sanctum::actingAs($customer);

        $this->postJson("/api/ke-hoach/{$plan->ma_ke_hoach}/lich-trinh-ai")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.generation_mode', 'fallback')
            ->assertJsonPath('data.created', 3);

        $this->assertDatabaseHas('hoat_dong_chi_tiet', [
            'ma_ke_hoach' => $plan->ma_ke_hoach,
            'ma_dia_diem' => $north->ma_dia_diem,
        ]);
    }

    public function test_customer_cannot_materialize_ai_itinerary_for_unowned_plan(): void
    {
        $owner = $this->createCustomer('KH000403', 'owner-403@example.com', '0901400403');
        $other = $this->createCustomer('KH000404', 'other-404@example.com', '0901400404');
        $ownerGroup = $this->createGroup('NHOMAI403', 'Nhom AI 403');
        $otherGroup = $this->createGroup('NHOMAI404', 'Nhom AI 404');
        $this->attachMember('TVAI403', $ownerGroup->Ma_nhom, $owner->Ma_khach_hang, 1);
        $this->attachMember('TVAI404', $otherGroup->Ma_nhom, $other->Ma_khach_hang, 1);

        $plan = $this->createPlan('PLANAI403', $ownerGroup->Ma_nhom);

        Sanctum::actingAs($other);

        $this->postJson("/api/ke-hoach/{$plan->ma_ke_hoach}/lich-trinh-ai")
            ->assertStatus(403);
    }

    public function test_customer_gets_fallback_draft_when_ai_service_is_temporarily_unavailable(): void
    {
        $customer = $this->createCustomer('KH000405', 'customer-405@example.com', '0901400405');
        $group = $this->createGroup('NHOMAI405', 'Nhom AI 405');
        $this->attachMember('TVAI405', $group->Ma_nhom, $customer->Ma_khach_hang, 1);

        $this->createLocation('DDAI405', 'Ho Guom Ha Noi', 'Hoan Kiem, Ha Noi', 105.8542, 21.0287);
        $this->createLocation('DDAI406', 'Van Mieu Ha Noi', 'Dong Da, Ha Noi', 105.8355, 21.0283);
        $plan = $this->createPlan('PLANAI405', $group->Ma_nhom, [
            'ten_ke_hoach' => 'Ha Noi',
            'mo_ta' => 'Tham quan trung tam Ha Noi',
        ]);

        $stub = new class extends AITourGuideService {
            public function generatePlan(string $diemDen, int $soNgay, string $nganSach, array $soThich, $diaDiems, $tours, array $selectedLocations = [], string $moTa = '', $selectedTour = null)
            {
                throw new \RuntimeException('503 Service Unavailable');
            }

            public function isRetriableFailure(\Throwable $exception): bool
            {
                return true;
            }
        };

        $this->app->instance(AITourGuideService::class, $stub);

        Sanctum::actingAs($customer);

        $this->postJson("/api/ke-hoach/{$plan->ma_ke_hoach}/lich-trinh-ai")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.generation_mode', 'fallback')
            ->assertJsonPath('data.created', 3);

        $this->assertDatabaseCount('dia_diem', 2);
        $this->assertDatabaseHas('hoat_dong_chi_tiet', [
            'ma_ke_hoach' => $plan->ma_ke_hoach,
            'ma_nhom' => $group->Ma_nhom,
            'gio_bat_dau' => '08:00:00',
            'gio_ket_thuc' => '10:00:00',
        ]);
    }

    public function test_fallback_draft_prefers_accented_northern_locations_for_mien_bac_plan(): void
    {
        $customer = $this->createCustomer('KH000406', 'customer-406@example.com', '0901400406');
        $group = $this->createGroup('NHOMAI406', 'Nhom AI 406');
        $this->attachMember('TVAI406', $group->Ma_nhom, $customer->Ma_khach_hang, 1);

        $northA = $this->createLocation('DDNORTH1', 'Hồ Hoàn Kiếm', 'Hà Nội', 105.8522, 21.0285);
        $northB = $this->createLocation('DDNORTH2', 'Vịnh Hạ Long', 'Quảng Ninh', 107.0448, 20.9101);
        $northC = $this->createLocation('DDNORTH3', 'Tràng An Ninh Bình', 'Ninh Bình', 105.886, 20.2506);
        $unrelated = $this->createLocation('DDSOUTH1', 'Trung tam Quan 1', 'Quan 1, TP Ho Chi Minh', 106.699, 10.7798);
        $plan = $this->createPlan('PLANAI406', $group->Ma_nhom, [
            'ten_ke_hoach' => 'Chuyen tham quan mien Bac',
            'mo_ta' => 'Tham quan cac dia diem lich su va canh quan mien Bac',
        ]);

        $stub = new class extends AITourGuideService {
            public function generatePlan(string $diemDen, int $soNgay, string $nganSach, array $soThich, $diaDiems, $tours, array $selectedLocations = [], string $moTa = '', $selectedTour = null)
            {
                throw new \RuntimeException('503 Service Unavailable');
            }

            public function isRetriableFailure(\Throwable $exception): bool
            {
                return true;
            }
        };

        $this->app->instance(AITourGuideService::class, $stub);

        Sanctum::actingAs($customer);

        $this->postJson("/api/ke-hoach/{$plan->ma_ke_hoach}/lich-trinh-ai")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.generation_mode', 'fallback')
            ->assertJsonPath('data.created', 3);

        $this->assertDatabaseMissing('hoat_dong_chi_tiet', [
            'ma_ke_hoach' => $plan->ma_ke_hoach,
            'ma_dia_diem' => $unrelated->ma_dia_diem,
        ]);

        foreach ([$northA, $northB, $northC] as $location) {
            $this->assertDatabaseHas('hoat_dong_chi_tiet', [
                'ma_ke_hoach' => $plan->ma_ke_hoach,
                'ma_dia_diem' => $location->ma_dia_diem,
            ]);
        }
    }

    public function test_customer_can_manage_plan_scoped_activities_for_owned_plan(): void
    {
        $customer = $this->createCustomer('KH000501', 'customer-501@example.com', '0901500501');
        $group = $this->createGroup('NHOMACT01', 'Nhom activity 501');
        $this->attachMember('TVACT501', $group->Ma_nhom, $customer->Ma_khach_hang, 1);

        $location = $this->createLocation('DDACT501', 'Ho Tay', 'Ha Noi', 105.8188, 21.0619);
        $plan = $this->createPlan('PLANACT501', $group->Ma_nhom);

        Sanctum::actingAs($customer);

        $create = $this->postJson("/api/ke-hoach/{$plan->ma_ke_hoach}/hoat-dong", [
            'ma_dia_diem' => $location->ma_dia_diem,
            'ngay_cu_the' => '2026-05-02',
            'gio_bat_dau' => '09:00',
            'gio_ket_thuc' => '11:00',
            'ghi_chu' => 'Ghi chu rieng cua khach',
        ]);

        $create->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ma_ke_hoach', $plan->ma_ke_hoach)
            ->assertJsonPath('data.ma_nhom', $group->Ma_nhom)
            ->assertJsonPath('data.ma_dia_diem', $location->ma_dia_diem);

        $activityId = $create->json('data.ma_hoat_dong_chi_tiet');

        $this->getJson("/api/ke-hoach/{$plan->ma_ke_hoach}/hoat-dong")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');

        $this->putJson("/api/ke-hoach/{$plan->ma_ke_hoach}/hoat-dong/{$activityId}", [
            'ngay_cu_the' => '2026-05-03',
            'gio_bat_dau' => '12:00',
            'gio_ket_thuc' => '14:00',
            'ghi_chu' => 'Da doi sang buoi trua',
        ])
            ->assertOk()
            ->assertJsonPath('data.gio_bat_dau', '12:00');

        $this->deleteJson("/api/ke-hoach/{$plan->ma_ke_hoach}/hoat-dong/{$activityId}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('hoat_dong_chi_tiet', [
            'ma_hoat_dong_chi_tiet' => $activityId,
        ]);
    }

    public function test_customer_cannot_manage_activities_for_unowned_plan(): void
    {
        $owner = $this->createCustomer('KH000502', 'owner-502@example.com', '0901500502');
        $other = $this->createCustomer('KH000503', 'other-503@example.com', '0901500503');
        $ownerGroup = $this->createGroup('NHOMACT02', 'Nhom owner 502');
        $otherGroup = $this->createGroup('NHOMACT03', 'Nhom other 503');
        $this->attachMember('TVACT502', $ownerGroup->Ma_nhom, $owner->Ma_khach_hang, 1);
        $this->attachMember('TVACT503', $otherGroup->Ma_nhom, $other->Ma_khach_hang, 1);

        $location = $this->createLocation('DDACT502', 'Van Mieu', 'Ha Noi', 105.8355, 21.0283);
        $plan = $this->createPlan('PLANACT502', $ownerGroup->Ma_nhom);
        $activity = $this->createActivity('HDACT502', $plan->ma_ke_hoach, $ownerGroup->Ma_nhom, $location->ma_dia_diem);

        Sanctum::actingAs($other);

        $this->getJson("/api/ke-hoach/{$plan->ma_ke_hoach}/hoat-dong")->assertStatus(403);
        $this->postJson("/api/ke-hoach/{$plan->ma_ke_hoach}/hoat-dong", [
            'ma_dia_diem' => $location->ma_dia_diem,
            'ngay_cu_the' => '2026-05-02',
            'gio_bat_dau' => '09:00',
            'gio_ket_thuc' => '11:00',
        ])->assertStatus(403);
        $this->deleteJson("/api/ke-hoach/{$plan->ma_ke_hoach}/hoat-dong/{$activity->ma_hoat_dong_chi_tiet}")
            ->assertStatus(403);
    }

    public function test_plan_scoped_activity_rejects_date_outside_plan_range(): void
    {
        $customer = $this->createCustomer('KH000504', 'customer-504@example.com', '0901500504');
        $group = $this->createGroup('NHOMACT04', 'Nhom activity 504');
        $this->attachMember('TVACT504', $group->Ma_nhom, $customer->Ma_khach_hang, 1);

        $location = $this->createLocation('DDACT504', 'Trang An', 'Ninh Binh', 105.886, 20.2506);
        $plan = $this->createPlan('PLANACT504', $group->Ma_nhom, [
            'ngay_bat_dau' => '2026-05-01',
            'ngay_ket_thuc' => '2026-05-03',
        ]);

        Sanctum::actingAs($customer);

        $this->postJson("/api/ke-hoach/{$plan->ma_ke_hoach}/hoat-dong", [
            'ma_dia_diem' => $location->ma_dia_diem,
            'ngay_cu_the' => '2026-05-05',
            'gio_bat_dau' => '09:00',
            'gio_ket_thuc' => '11:00',
        ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    private function createCustomer(string $maKhachHang, string $email, string $phone): KhachHang
    {
        return KhachHang::query()->create([
            'Ma_khach_hang' => $maKhachHang,
            'Ho_va_ten' => "Khach {$maKhachHang}",
            'Mat_khau' => Hash::make('demo123'),
            'Email' => $email,
            'Ngay_sinh' => '2000-01-01',
            'Gioi_tinh' => true,
            'so_dien_thoai' => $phone,
            'is_block' => true,
        ]);
    }

    private function createAdmin(): Admin
    {
        DB::table('chuc_vu')->insert([
            'ma_chuc_vu' => 'CVLOCK01',
            'ten_chuc_vu' => 'Admin Lock',
            'tinh_trang' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Admin::query()->create([
            'Ma_admin' => 'ADLOCK01',
            'Ho_va_ten' => 'Admin Hardening',
            'Mat_khau' => Hash::make('demo123'),
            'Email' => 'admin.lock@example.com',
            'Ngay_sinh' => '1990-01-01',
            'Gioi_tinh' => true,
            'ma_chuc_vu' => 'CVLOCK01',
            'is_block' => false,
            'so_dien_thoai' => '0901000099',
        ]);
    }

    private function createGroup(string $maNhom, string $tenNhom): Nhom
    {
        return Nhom::query()->create([
            'Ma_nhom' => $maNhom,
            'ten_nhom' => $tenNhom,
        ]);
    }

    private function attachMember(string $maThanhVien, string $maNhom, string $maKhachHang, int $vaiTro): ThanhVienNhom
    {
        return ThanhVienNhom::query()->create([
            'Ma_thanh_vien' => $maThanhVien,
            'Ma_nhom' => $maNhom,
            'Ma_khach_hang' => $maKhachHang,
            'vai_tro' => $vaiTro,
        ]);
    }

    private function createPlan(string $maKeHoach, string $maNhom, array $overrides = []): KeHoach
    {
        return KeHoach::query()->create(array_merge([
            'ma_ke_hoach' => $maKeHoach,
            'ma_nhom' => $maNhom,
            'ten_ke_hoach' => 'Hanh trinh AI',
            'mo_ta' => 'Lich trinh test',
            'so_nguoi' => 2,
            'ngay_bat_dau' => '2026-05-01',
            'ngay_ket_thuc' => '2026-05-03',
            'ngan_sach_du_kien' => 3000000,
            'tong_chi_phi' => 0,
            'trang_thai' => true,
            'nguon_tao' => 'manual',
        ], $overrides));
    }

    private function createLocation(string $maDiaDiem, string $tenDiaDiem, string $diaChi, float $kinhDo, float $viDo): DiaDiem
    {
        return DiaDiem::query()->create([
            'ma_dia_diem' => $maDiaDiem,
            'ten_dia_diem' => $tenDiaDiem,
            'loai' => 1,
            'dia_chi' => $diaChi,
            'kinh_do' => $kinhDo,
            'vi_do' => $viDo,
            'gia_giao_dong' => 0,
        ]);
    }

    private function createActivity(string $maHoatDong, string $maKeHoach, string $maNhom, string $maDiaDiem): HoatDongChiTiet
    {
        return HoatDongChiTiet::query()->create([
            'ma_hoat_dong_chi_tiet' => $maHoatDong,
            'ma_ke_hoach' => $maKeHoach,
            'ma_nhom' => $maNhom,
            'ma_dia_diem' => $maDiaDiem,
            'gio_bat_dau' => '08:00:00',
            'gio_ket_thuc' => '10:00:00',
            'ngay_cu_the' => '2026-05-01',
        ]);
    }
}
