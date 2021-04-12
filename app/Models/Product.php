<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'description', 'image', 'category_id'
    ];

    public function getResults(array $data, int $totalPage)
    {
        if (!isset($data['filter']) && !isset($data['name']) && !isset($data['description']))
            return $this->paginate(10);

        return $this->where(function ($query) use ($data) {
                   if (isset($data['filter'])) {
                       $filter = $data['filter'];
                       $query->where('name', $filter);
                       $query->orwhere('description', 'LIKE', "%{$filter}%");
                   }

                    if (isset($data['name']))
                        $query->where('name', $data['name']);

                    if (isset($data['description'])) {
                        $description = $data['description'];
                        $query->where('description', 'LIKE', "%{$description}%");
                    }
        })->paginate($totalPage);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}