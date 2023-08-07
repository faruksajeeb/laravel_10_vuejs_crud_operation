<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'category_id',
        'description',
        'price',
        'feature_image',
        'gallery_images',
    ];

    public function scopeSearch($query,$term){
        $term = "%$term%";         
        $query->where(function($q) use($term){
            $q->where('id','LIKE',$term);
            $q->orWhere('name','LIKE',$term);
            $q->orWhere('description','LIKE',$term);
            $q->orWhere('price','LIKE',$term);
            // $q->orWhereHas('categories',function($q) use($term){
            //     $q->where('category_name','LIKE',$term);
            // });
            // $q->orWhereHas('sub_categories',function($q) use($term){
            //     $q->where('sub_category_name','LIKE',$term);
            // });
        });
    }
}
