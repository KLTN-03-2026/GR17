<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SonModuleRoutesTest extends TestCase
{
    public function test_son_module_routes_are_registered(): void
    {
        $routes = collect(Route::getRoutes())
            ->map(fn ($route) => implode('|', $route->methods()) . ' ' . $route->uri())
            ->all();

        $expectedRoutes = [
            'GET|HEAD api/nhom',
            'GET|HEAD api/nhom/search',
            'POST api/nhom',
            'GET|HEAD api/thanh-vien-nhom',
            'GET|HEAD api/thanh-vien-nhom/search',
            'GET|HEAD api/thanh-vien-nhom/nhom/{maNhom}',
            'GET|HEAD api/danh-gia-ke-hoach',
            'GET|HEAD api/danh-gia-ke-hoach/search',
            'GET|HEAD api/danh-gia-ke-hoach/summary',
            'GET|HEAD api/danh-gia-ke-hoach/dia-diem/{maDiaDiem}',
            'POST api/danh-gia-ke-hoach',
            'GET|HEAD api/admin/hoa-don',
            'GET|HEAD api/admin/hoa-don/summary',
            'PATCH api/admin/hoa-don/{ma_hoa_don}/status',
            'GET|HEAD api/admin/hoa-don/nhom/{maNhom}',
        ];

        foreach ($expectedRoutes as $expectedRoute) {
            $this->assertContains($expectedRoute, $routes);
        }
    }
}
