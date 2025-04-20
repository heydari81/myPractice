<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'quantity',
        'image',
        'discount'
    ];
    public function newProduct($request)
    {   $imagePath = Carbon::now()->microsecond.'.'.$request->image->extension();
        $data = $request->only(['name', 'slug', 'description', 'price', 'quantity','discount']);
        if ($request->hasFile('image')) {
            $request->image->storeAs('images', $imagePath,'public');
            $data['image'] = $imagePath;
        }
        return $this->query()->create([
            'name'=> $data['name'],
            'slug'=>$data['slug'],
            'description'=>$data['description'],
            'price'=>$data['price'],
            'quantity'=>$data['quantity'],
            'image'=>$imagePath,
            'discount'=>$data['discount']
        ]);
    }

    public function updateProduct($request)
    {
        $imagePath = null;
        $data = $request->only(['name', 'slug', 'description', 'price', 'quantity','discount']);
        if ($request->hasFile('image')) {
            $imagePath = Carbon::now()->microsecond.'.'.$request->image->extension();
            $request->image->storeAs('images', $imagePath,'public');
            $data['image'] = $imagePath;        }
        return $this->update([
            'name'=> $data['name'],
            'slug'=>$data['slug'],
            'description'=>$data['description'],
            'price'=>$data['price'],
            'quantity'=>$data['quantity'],
            'discount'=>$data['discount'],
            'image' => $request->hasFile('image') ? $imagePath : $this->image,        ]);
    }
    protected static function booted()
    {
        static::created(function () {
            Cache::forget('products');
        });

        static::updated(function () {
            Cache::forget('products');
        });

        static::deleted(function () {
            Cache::forget('products');
        });
    }
}
