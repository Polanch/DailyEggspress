<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'thumbnail',
        'tags',
        'blog_title',
        'blog_content',
        'views_count',
        'blog_status',
        'scheduled_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'scheduled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class);
    }

    public function reactions()
    {
        return $this->hasMany(BlogReaction::class);
    }

    public function views()
    {
        return $this->hasMany(BlogView::class);
    }
}
