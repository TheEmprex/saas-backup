@extends('theme::layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Message Folders</h1>
                    <p class="text-gray-600">Organize your conversations with custom folders</p>
                </div>
                <a href="{{ route('messages.folders.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    New Folder
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Folders List -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Your Folders</h2>
                    
                    <div class="space-y-2" id="folders-list">
                        @forelse($folders as $folder)
                        <div class="folder-item p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors" data-folder-id="{{ $folder->id }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <!-- Folder Color -->
                                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $folder->color }}"></div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $folder->name }}</h3>
                                        @if($folder->description)
                                        <p class="text-sm text-gray-500">{{ $folder->description }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $folder->messages_count }} messages
                                    </span>
                                    @if($folder->unread_count > 0)
                                    <span class="block mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $folder->unread_count }} unread
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            @if(!$folder->is_default)
                            <div class="mt-2 flex space-x-2">
                                <a href="{{ route('messages.folders.edit', $folder) }}" class="text-xs text-blue-600 hover:text-blue-800">Edit</a>
                                <form action="{{ route('messages.folders.destroy', $folder) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? Messages will be moved to your Inbox.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 10 5 5 5-5" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No folders</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first folder.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Folder Content -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div id="folder-content">
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.962 8.962 0 01-4.129-1.005L3 21l1.005-5.871A8.962 8.962 0 013 12a8 8 0 0116 0z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Select a folder</h3>
                            <p class="mt-2 text-sm text-gray-500">Choose a folder from the left to view its messages.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Folder Management Tools -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Folder Management</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Move Messages -->
                <div class="p-4 border border-gray-200 rounded-lg">
                    <h3 class="font-medium text-gray-900 mb-2">Move Messages</h3>
                    <p class="text-sm text-gray-600 mb-3">Select messages from any folder and move them to another folder.</p>
                    <button class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm rounded" onclick="toggleMoveMode()">
                        Start Moving
                    </button>
                </div>
                
                <!-- Reorder Folders -->
                <div class="p-4 border border-gray-200 rounded-lg">
                    <h3 class="font-medium text-gray-900 mb-2">Reorder Folders</h3>
                    <p class="text-sm text-gray-600 mb-3">Drag and drop folders to change their order in the sidebar.</p>
                    <button class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm rounded" onclick="toggleReorderMode()">
                        Reorder
                    </button>
                </div>
                
                <!-- Folder Settings -->
                <div class="p-4 border border-gray-200 rounded-lg">
                    <h3 class="font-medium text-gray-900 mb-2">Auto-Organization</h3>
                    <p class="text-sm text-gray-600 mb-3">Set up rules to automatically organize incoming messages.</p>
                    <button class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm rounded">
                        Coming Soon
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load folder content when clicked
    document.querySelectorAll('.folder-item').forEach(item => {
        item.addEventListener('click', function() {
            const folderId = this.dataset.folderId;
            loadFolderContent(folderId);
            
            // Update active state
            document.querySelectorAll('.folder-item').forEach(f => f.classList.remove('bg-blue-50', 'border-blue-200'));
            this.classList.add('bg-blue-50', 'border-blue-200');
        });
    });
});

function loadFolderContent(folderId) {
    const contentDiv = document.getElementById('folder-content');
    contentDiv.innerHTML = '<div class="flex justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';
    
    fetch(`/messages/folders/${folderId}`)
        .then(response => response.text())
        .then(html => {
            contentDiv.innerHTML = html;
        })
        .catch(error => {
            contentDiv.innerHTML = '<div class="text-center py-8 text-red-600">Error loading folder content</div>';
        });
}

function toggleMoveMode() {
    // Implementation for message moving functionality
    alert('Move mode would be implemented here');
}

function toggleReorderMode() {
    // Implementation for folder reordering
    alert('Reorder mode would be implemented here');
}
</script>
@endpush
@endsection
