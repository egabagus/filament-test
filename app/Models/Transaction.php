<?php

namespace App\Models;

use App\Observers\TransactionObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([TransactionObserver::class])]
class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "header_transaction";
    public $timestamps = true;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $month = Carbon::now()->format('m');
            $year = Carbon::now()->format('Y');

            // Mendapatkan data terakhir berdasarkan kode
            $lastest = self::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->latest('code')
                ->first();

            // Jika data ada, ambil 4 angka terakhir dan tambahkan 1, jika tidak mulai dari 1
            $code = $lastest ? (int) substr($lastest->code, -4) + 1 : 1;

            // Membuat format code baru dengan 4 angka terakhir yang berubah
            $model->code = 'TR' . $year . $month . str_pad($code, 4, '0', STR_PAD_LEFT);
        });
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailTransaction::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function cust(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
