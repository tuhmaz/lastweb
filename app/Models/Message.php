<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Message extends Model
{
    protected $fillable = ['sender_id', 'conversation_id', 'subject', 'body', 'read', 'is_important', 'is_draft'];

    public function scopeForUser(Builder $query, $userId)
    {
        return $query->whereHas('conversation', function ($q) use ($userId) {
            $q->where('user1_id', $userId)->orWhere('user2_id', $userId);
        });
    }

    public function scopeUnread(Builder $query)
    {
        return $query->where('read', false);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}