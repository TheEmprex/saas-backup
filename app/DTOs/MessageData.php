<?php

namespace App\DTOs;

class MessageData
{
    public function __construct(
        public readonly int $senderId,
        public readonly ?string $content = null,
        public readonly ?int $conversationId = null,
        public readonly ?int $recipientId = null,
        public readonly string $messageType = 'text',
        public readonly ?string $fileUrl = null,
        public readonly ?string $fileName = null,
        public readonly ?int $fileSize = null,
        public readonly ?int $replyToId = null,
        public readonly ?array $metadata = null
    ) {}

    public static function fromRequest(array $data, int $senderId): self
    {
        return new self(
            senderId: $senderId,
            content: $data['content'] ?? null,
            conversationId: $data['conversation_id'] ?? null,
            recipientId: $data['recipient_id'] ?? null,
            messageType: $data['message_type'] ?? 'text',
            fileUrl: $data['file_url'] ?? null,
            fileName: $data['file_name'] ?? null,
            fileSize: $data['file_size'] ?? null,
            replyToId: $data['reply_to_id'] ?? null,
            metadata: $data['metadata'] ?? null
        );
    }

    public static function withFile(
        int $senderId,
        ?string $content,
        array $fileData,
        ?int $conversationId = null,
        ?int $recipientId = null,
        ?int $replyToId = null
    ): self {
        return new self(
            senderId: $senderId,
            content: $content,
            conversationId: $conversationId,
            recipientId: $recipientId,
            messageType: $fileData['message_type'],
            fileUrl: $fileData['file_url'],
            fileName: $fileData['file_name'],
            fileSize: $fileData['file_size'],
            replyToId: $replyToId
        );
    }

    public function hasFile(): bool
    {
        return !empty($this->fileUrl);
    }

    public function isTextMessage(): bool
    {
        return $this->messageType === 'text';
    }

    public function toArray(): array
    {
        return [
            'sender_id' => $this->senderId,
            'content' => $this->content,
            'conversation_id' => $this->conversationId,
            'recipient_id' => $this->recipientId,
            'message_type' => $this->messageType,
            'file_url' => $this->fileUrl,
            'file_name' => $this->fileName,
            'file_size' => $this->fileSize,
            'reply_to_id' => $this->replyToId,
            'metadata' => $this->metadata,
        ];
    }
}
