<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'job_post_id',
        'job_application_id',
        'message_content',
        'attachments',
        'message_type',
        'read_at',
        'is_read',
        'is_archived',
        'thread_id',
        'parent_message_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_archived' => 'boolean',
        'read_at' => 'datetime',
        'attachments' => 'array',
    ];

    protected $appends = ['formatted_attachments'];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function parentMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'parent_message_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_message_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeConversation($query, $userId1, $userId2)
    {
        return $query->where(function ($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId1)->where('recipient_id', $userId2);
        })->orWhere(function ($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId2)->where('recipient_id', $userId1);
        });
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function hasAttachments()
    {
        return !empty($this->attachments);
    }

    public function getFormattedAttachmentsAttribute()
    {
        if (empty($this->attachments)) {
            return [];
        }

        return collect($this->attachments)->map(function ($attachment) {
            return [
                'name' => $attachment['name'] ?? 'Unknown',
                'size' => $attachment['size'] ?? 0,
                'type' => $attachment['type'] ?? 'unknown',
                'url' => $attachment['path'] ? Storage::url($attachment['path']) : null,
                'icon' => $this->getFileIcon($attachment['type'] ?? 'unknown'),
                'is_image' => str_starts_with($attachment['type'] ?? '', 'image/'),
                'is_audio' => str_starts_with($attachment['type'] ?? '', 'audio/'),
                'is_video' => str_starts_with($attachment['type'] ?? '', 'video/'),
                'is_document' => in_array($attachment['type'] ?? '', [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'text/plain'
                ]),
                'formatted_size' => $this->formatFileSize($attachment['size'] ?? 0)
            ];
        })->toArray();
    }

    private function getFileIcon($mimeType)
    {
        $icons = [
            'image/' => '🖼️',
            'audio/' => '🎵',
            'video/' => '🎬',
            'application/pdf' => '📄',
            'application/msword' => '📝',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '📝',
            'text/plain' => '📝',
            'application/zip' => '📦',
            'application/x-rar-compressed' => '📦',
        ];

        foreach ($icons as $type => $icon) {
            if (str_starts_with($mimeType, $type)) {
                return $icon;
            }
        }

        return '📎';
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1024 * 1024 * 1024) {
            return round($bytes / (1024 * 1024 * 1024), 2) . ' GB';
        } elseif ($bytes >= 1024 * 1024) {
            return round($bytes / (1024 * 1024), 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
}
