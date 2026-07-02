<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentSlide extends Model
{
    protected $fillable = [
        'content_project_id', 'slide_number', 'title', 'body',
        'visual_instruction', 'layout_type', 'image_path',
    ];

    public function contentProject() { return $this->belongsTo(ContentProject::class); }
}
