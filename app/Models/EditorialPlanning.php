<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditorialPlanning extends Model
{
    protected $fillable = [
        'client_id',
        'content_project_id',
        'theme',
        'objective',
        'channel',
        'format',
        'planned_for',
        'priority',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'planned_for' => 'date',
            'priority' => 'integer',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function contentProject(): BelongsTo
    {
        return $this->belongsTo(ContentProject::class);
    }
}
