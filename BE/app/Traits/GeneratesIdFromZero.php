<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait GeneratesIdFromZero
{
    /**
     * Boot the GeneratesIdFromZero trait for a model.
     *
     * @return void
     */
    public static function bootGeneratesIdFromZero()
    {
        static::creating(function ($model) {
            $primaryKey = $model->getKeyName();

            if (empty($model->{$primaryKey})) {
                $table = $model->getTable();
                
                // Lấy giá trị lớn nhất của khóa chính dưới dạng số để tăng dần
                $maxId = DB::table($table)->max(DB::raw('CAST(' . $primaryKey . ' AS UNSIGNED)'));

                if (is_null($maxId)) {
                    $model->{$primaryKey} = '0';
                } else {
                    $model->{$primaryKey} = (string) (((int) $maxId) + 1);
                }
            }
        });
    }
}
