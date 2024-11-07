<?php

namespace App\Filament\Resources\CategoryResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\CategoryResource;

class CreateHandler extends Handlers
{
    public static string | null $uri = '/';
    public static string | null $resource = CategoryResource::class;
    // public static bool $public = true;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    public function handler(Request $request)
    {
        $model = new (static::getModel());
        // dd($request->except(['code']));
        $model->fill($request->except(['code']));

        $validated = $request->validate([
            'code' => 'required',
            'name' => 'required',
        ]);

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}
