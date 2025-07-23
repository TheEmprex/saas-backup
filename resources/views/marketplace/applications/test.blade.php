@if($applications->count() > 0)
    <div>
        @foreach($applications as $application)
            <div>{{ $application->id }}</div>
        @endforeach
    </div>
@else
    <div>No applications</div>
@endif
