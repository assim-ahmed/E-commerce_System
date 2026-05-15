<?php

namespace App\Repositories;

use App\Contracts\Repositories\CouponRepositoryInterface;
use App\Models\Coupon;

class CouponRepository implements CouponRepositoryInterface
{
    public function findByCode(string $code)
    {
        return Coupon::where('code', $code)->first();
    }

    public function incrementUsage(string $code)
    {
        Coupon::where('code', $code)->increment('used_count');
    }

    public function find(int $id)
    {
        return Coupon::find($id);
    }

    public function all()
    {
        return Coupon::all();
    }

    public function create(array $data)
    {
        return Coupon::create($data);
    }

    public function update(int $id, array $data)
    {
        $coupon = Coupon::find($id);
        if ($coupon) {
            $coupon->update($data);
            return $coupon;
        }
        return null;
    }

    public function delete(int $id)
    {
        return Coupon::destroy($id);
    }
}