<?php

namespace App\Http\Requests\Messaging;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'conversation_id' => 'nullable|exists:conversations,id',
            'recipient_id' => 'nullable|exists:users,id',
            'content' => 'required_without:file|string|max:10000',
            'message_type' => ['in:text,image,video,audio,file,call'],
            'file' => [
                'nullable',
                'file',
                'max:50000', // 50MB max
                Rule::requiredIf(fn() => $this->input('message_type') !== 'text' && !$this->input('content'))
            ],
            'reply_to_id' => 'nullable|exists:messages,id',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'content.required_without' => 'Message content is required when no file is provided.',
            'file.required_if' => 'File is required for non-text message types.',
            'file.max' => 'File size cannot exceed 50MB.',
            'conversation_id.exists' => 'The selected conversation does not exist.',
            'recipient_id.exists' => 'The selected recipient does not exist.',
            'reply_to_id.exists' => 'The message you are replying to does not exist.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default message type if not provided
        if (!$this->has('message_type')) {
            $this->merge(['message_type' => 'text']);
        }

        // If replying to a message, ensure conversation_id is set
        if ($this->has('reply_to_id') && !$this->has('conversation_id')) {
            $replyToMessage = \App\Models\Message::find($this->input('reply_to_id'));
            if ($replyToMessage) {
                $this->merge(['conversation_id' => $replyToMessage->conversation_id]);
            }
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Ensure either conversation_id or recipient_id is provided
            if (!$this->input('conversation_id') && !$this->input('recipient_id')) {
                $validator->errors()->add(
                    'conversation_id', 
                    'Either conversation_id or recipient_id must be provided.'
                );
            }

            // Ensure user doesn't message themselves
            if ($this->input('recipient_id') === auth()->id()) {
                $validator->errors()->add(
                    'recipient_id', 
                    'You cannot send a message to yourself.'
                );
            }

            // Validate file type matches message type
            if ($this->hasFile('file') && $this->input('message_type') !== 'text') {
                $file = $this->file('file');
                $mimeType = $file->getMimeType();
                $messageType = $this->input('message_type');

                $validMimeTypes = [
                    'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
                    'video' => ['video/mp4', 'video/webm', 'video/quicktime'],
                    'audio' => ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4'],
                ];

                if (isset($validMimeTypes[$messageType]) && 
                    !in_array($mimeType, $validMimeTypes[$messageType]) && 
                    $messageType !== 'file') {
                    $validator->errors()->add(
                        'file', 
                        "File type does not match the specified message type: {$messageType}."
                    );
                }
            }
        });
    }
}
