<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentGeneration extends Model
{
    protected $fillable = [
        'content_project_id', 'provider', 'model', 'input_data', 'output_data',
        'metadata', 'tokens_used', 'latency_ms',
    ];

    protected $casts = [
        'input_data' => 'array',
        'output_data' => 'array',
        'metadata' => 'array',
    ];

    public function contentProject() { return $this->belongsTo(ContentProject::class); }
}
