<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\KeHoach;
use App\Models\KhachHang;
use App\Models\Nhom;
use App\Models\ThanhVienNhom;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

class CustomerOwnershipService
{
    public function isAdmin(?Authenticatable $user): bool
    {
        return $user instanceof Admin;
    }

    public function isCustomer(?Authenticatable $user): bool
    {
        return $user instanceof KhachHang;
    }

    public function ensureCustomerOrAdmin(?Authenticatable $user): bool
    {
        return $this->isAdmin($user) || $this->isCustomer($user);
    }

    public function groupIdsForCustomer(string $maKhachHang): Collection
    {
        return ThanhVienNhom::query()
            ->where('Ma_khach_hang', $maKhachHang)
            ->pluck('Ma_nhom');
    }

    public function customerBelongsToGroup(string $maKhachHang, string $maNhom): bool
    {
        return ThanhVienNhom::query()
            ->where('Ma_khach_hang', $maKhachHang)
            ->where('Ma_nhom', $maNhom)
            ->exists();
    }

    public function customerLeadsGroup(string $maKhachHang, string $maNhom): bool
    {
        return ThanhVienNhom::query()
            ->where('Ma_khach_hang', $maKhachHang)
            ->where('Ma_nhom', $maNhom)
            ->where('vai_tro', 1)
            ->exists();
    }

    public function canAccessGroup(?Authenticatable $user, string $maNhom): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if (!$user instanceof KhachHang) {
            return false;
        }

        return $this->customerBelongsToGroup($user->Ma_khach_hang, $maNhom);
    }

    public function canManageGroup(?Authenticatable $user, string $maNhom): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if (!$user instanceof KhachHang) {
            return false;
        }

        return $this->customerLeadsGroup($user->Ma_khach_hang, $maNhom);
    }

    public function canAccessPlan(?Authenticatable $user, KeHoach $keHoach): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if (!$user instanceof KhachHang || !is_string($keHoach->ma_nhom) || $keHoach->ma_nhom === '') {
            return false;
        }

        return $this->customerBelongsToGroup($user->Ma_khach_hang, $keHoach->ma_nhom);
    }

    public function visibleGroupsQuery(?Authenticatable $user)
    {
        if ($this->isAdmin($user)) {
            return Nhom::query();
        }

        /** @var \App\Models\KhachHang $user */
        return Nhom::query()
            ->whereIn('Ma_nhom', $this->groupIdsForCustomer($user->Ma_khach_hang));
    }
}
