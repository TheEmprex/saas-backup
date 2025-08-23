@extends('theme::app')

@section('title', 'Mail · Inbox')

@section('content')
<div class="container mx-auto px-4 py-6">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Inbox</h1>
    <a href="{{ route('mail.compose') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Compose</a>
  </div>

  @if(session('status'))
    <div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('status') }}</div>
  @endif

  @if($conversations->isEmpty())
    <div class="p-8 text-center text-zinc-500 bg-white rounded-lg border">No conversations yet.</div>
  @else
    <div class="bg-white rounded-lg border divide-y">
      @foreach($conversations as $conv)
        @php
          $other = $conv->user1_id === auth()->id() ? $conv->user2 : $conv->user1;
        @endphp
        <a href="{{ route('mail.show', $conv) }}" class="block px-4 py-3 hover:bg-zinc-50">
          <div class="flex items-center justify-between">
            <div class="font-medium">{{ $other?->name ?? 'User #'.$other?->id }}</div>
            <div class="text-sm text-zinc-500">{{ optional($conv->last_message_at ?? $conv->updated_at)->diffForHumans() }}</div>
          </div>
          <div class="text-sm text-zinc-600 truncate">
            {{ $conv->lastMessage?->content ?? 'No messages yet' }}
          </div>
        </a>
      @endforeach
    </div>

    <div class="mt-4">{{ $conversations->links() }}</div>
  @endif
</div>
@endsection

