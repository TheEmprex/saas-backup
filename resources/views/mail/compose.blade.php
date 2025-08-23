@extends('theme::app')

@section('title', 'Mail · Compose')

@section('content')
<div class="container mx-auto px-4 py-6">
  <h1 class="text-2xl font-semibold mb-6">Compose</h1>

  @if($errors->any())
    <div class="mb-4 p-3 rounded bg-red-50 text-red-700">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('mail.send') }}" class="bg-white p-6 rounded-lg border space-y-4">
    @csrf

    <div>
      <label class="block text-sm font-medium mb-1">To</label>
      <input type="text" name="to" value="{{ old('to') }}" placeholder="username or email" class="w-full border rounded px-3 py-2">
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Subject (optional)</label>
      <input type="text" name="subject" value="{{ old('subject') }}" class="w-full border rounded px-3 py-2">
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Message</label>
      <textarea name="content" rows="8" class="w-full border rounded px-3 py-2" placeholder="Write your message...">{{ old('content') }}</textarea>
    </div>

    <div class="flex items-center justify-end space-x-2">
      <a href="{{ route('mail.index') }}" class="px-4 py-2 border rounded">Cancel</a>
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Send</button>
    </div>
  </form>
</div>
@endsection

