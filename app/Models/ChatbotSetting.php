<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotKnowledge extends Model
{
    protected $table = 'chatbot_knowledge';

    protected $fillable = [
        'question', 'answer', 'keywords', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getKeywordsArrayAttribute(): array
    {
        if (! $this->keywords) {
            return [];
        }
        return array_filter(array_map('trim', explode(',', $this->keywords)));
    }
}
