<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdBox extends Model
{
    protected $fillable = [
        'title',
        'url',
        'description',
        'image_path',
        'html_content',
        'order',
        'is_active',
        'click_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'click_count' => 'integer',
    ];

    public function incrementClicks(): void
    {
        $this->increment('click_count');
    }
}
