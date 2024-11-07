<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "item";
    public $timestamps = true;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $lastest = self::latest('code')->first();
            $code = $lastest ? (int) substr($lastest->code, 2) + 1 : 1;
            $model->code = 'IT' . str_pad($code, 7, '0', STR_PAD_LEFT);
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
