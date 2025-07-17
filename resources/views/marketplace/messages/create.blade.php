@extends('theme::app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Send Message</h1>
                <div class="page-pretitle">Start a conversation</div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">New Message</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('marketplace.messages.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">To</label>
                            @if(isset($user))
                                <input type="hidden" name="conversation_id" value="{{ $user->id }}">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3" style="background-image: url('/images/default-avatar.png')"></div>
                                    <div>
                                        <div class="font-weight-medium">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->userType->display_name }}</div>
                                    </div>
                                </div>
                            @else
                                <select name="conversation_id" class="form-select" required>
                                    <option value="">Select a user...</option>
                                    @foreach($users as $availableUser)
                                        <option value="{{ $availableUser->id }}">
                                            {{ $availableUser->name }} ({{ $availableUser->userType->display_name }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="content" class="form-control" rows="8" placeholder="Type your message..." required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Attachment (Optional)</label>
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                            <div class="form-text">Max file size: 10MB</div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('marketplace.messages') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-send me-2"></i>Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
