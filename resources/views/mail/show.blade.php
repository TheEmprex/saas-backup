@extends('theme::app')

@section('title', 'Mail · Thread')

@section('content')
<div class="container mx-auto px-4 py-6">
  <div class="flex items-center justify-between mb-4">
    @php
      $other = $conversation->user1_id === auth()->id() ? $conversation->user2 : $conversation->user1;
    @endphp
    <h1 class="text-xl font-semibold">{{ $other?->name ?? 'Conversation' }}</h1>
    <div class="space-x-2">
      <a href="{{ route('mail.index') }}" class="px-3 py-1 border rounded">Inbox</a>
      <a href="{{ route('mail.compose') }}" class="px-3 py-1 bg-blue-600 text-white rounded">Compose</a>
    </div>
  </div>

  <div class="bg-white border rounded-lg divide-y">
    @foreach($messages as $msg)
      <div class="px-4 py-3">
        <div class="flex items-center justify-between mb-1">
          <div class="text-sm text-zinc-600">
            From: <span class="font-medium">{{ $msg->sender?->name ?? ('User #'.$msg->sender_id) }}</span>
          </div>
          <div class="text-xs text-zinc-500">
            {{ $msg->created_at->format('Y-m-d H:i') }}
          </div>
        </div>
        @if(($msg->metadata['subject'] ?? null))
          <div class="text-sm font-medium mb-1">Subject: {{ $msg->metadata['subject'] }}</div>
        @endif
        <div class="whitespace-pre-line">{{ $msg->content }}</div>
      </div>
    @endforeach
  </div>

  <div class="mt-4">{{ $messages->links() }}</div>

  <div class="mt-6 bg-white border rounded-lg p-4">
    <form method="POST" action="{{ route('mail.send') }}" class="space-y-3">
      @csrf
      <input type="hidden" name="to" value="{{ $other?->email ?? $other?->username }}">
      <div>
        <label class="block text-sm font-medium mb-1">Subject (optional)</label>
        <input type="text" name="subject" class="w-full border rounded px-3 py-2" placeholder="Re: optional subject">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Message</label>
        <textarea name="content" rows="6" class="w-full border rounded px-3 py-2" placeholder="Write your reply..."></textarea>
      </div>
      <div class="flex justify-end">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Send</button>
      </div>
    </form>
  </div>
</div>
@endsection

