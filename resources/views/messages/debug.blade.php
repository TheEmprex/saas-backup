@extends('theme::app')

@section('title', 'Messages - Debug')

@section('content')
<div class="min-h-screen bg-slate-50 p-4">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-bold text-slate-900 mb-6">🐛 Messages Debug Interface</h1>
        
        <!-- Debug Info -->
        <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
            <h2 class="text-lg font-semibold mb-4">Debug Information</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><strong>User ID:</strong> {{ auth()->id() }}</div>
                <div><strong>User Name:</strong> {{ auth()->user()->name }}</div>
                <div><strong>CSRF Token:</strong> {{ csrf_token() }}</div>
                <div><strong>Current Time:</strong> {{ now() }}</div>
            </div>
        </div>
        
        <!-- Simple Message Form -->
        <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
            <h2 class="text-lg font-semibold mb-4">Test Message Form</h2>
            <form id="testForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Recipient</label>
                    <input type="text" name="recipient" placeholder="Enter recipient name" class="w-full p-2 border border-slate-200 rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Message</label>
                    <textarea name="message" rows="3" placeholder="Type your message..." class="w-full p-2 border border-slate-200 rounded"></textarea>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Send Test Message</button>
            </form>
        </div>
        
        <!-- Test Results -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Test Results</h2>
            <div id="results" class="p-4 bg-slate-50 rounded border min-h-[100px]">
                <p class="text-slate-500">Test results will appear here...</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('testForm');
    const results = document.getElementById('results');
    
    // Test API endpoints
    async function testEndpoints() {
        const tests = [
            { name: 'Get Conversations', url: '/messages/conversations' },
            { name: 'CSRF Token', url: '/csrf-token' },
        ];
        
        for (const test of tests) {
            try {
                const response = await fetch(test.url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                console.log(`${test.name}:`, data);
                
                const div = document.createElement('div');
                div.innerHTML = `<strong>${test.name}:</strong> ${response.ok ? '✅' : '❌'} (${response.status})`;
                results.appendChild(div);
            } catch (error) {
                console.error(`${test.name} error:`, error);
                const div = document.createElement('div');
                div.innerHTML = `<strong>${test.name}:</strong> ❌ Error: ${error.message}`;
                results.appendChild(div);
            }
        }
    }
    
    // Auto-run tests on load
    results.innerHTML = '<p>Running tests...</p>';
    testEndpoints();
    
    // Handle form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = {
            recipient: formData.get('recipient'),
            message: formData.get('message')
        };
        
        try {
            const response = await fetch('/messages/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    content: data.message,
                    recipient_id: 1 // Test with user ID 1
                })
            });
            
            const result = await response.json();
            const div = document.createElement('div');
            div.innerHTML = `<strong>Send Message Test:</strong> ${response.ok ? '✅' : '❌'} ${JSON.stringify(result)}`;
            results.appendChild(div);
        } catch (error) {
            const div = document.createElement('div');
            div.innerHTML = `<strong>Send Message Error:</strong> ❌ ${error.message}`;
            results.appendChild(div);
        }
    });
});
</script>
@endsection
