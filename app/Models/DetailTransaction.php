<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "detail_transaction";
    public $timestamps = true;
    protected $guarded = [];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function header(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
