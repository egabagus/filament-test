<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Http;
use Sushi\Sushi;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    // use Sushi;

    protected $table = "product";
    public $timestamps = true;
    protected $guarded = [];

    // public function getRows(): array
    // {
    //     $apiCall = Http::asJson()
    //         ->acceptJson()
    //         ->get("http://cms.test/client/master/product/data/user");

    //     dd($apiCall->json());

    //     return $apiCall->json('data');
    // }
}
