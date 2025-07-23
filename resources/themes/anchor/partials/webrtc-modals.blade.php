<div id="incoming-call-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 bg-slate-800/75"></div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl p-8 text-center z-10 max-w-sm mx-auto">
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Incoming Call</h3>
            <div class="my-6">
                <div id="incoming-caller-avatar" class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-4xl mx-auto shadow-lg"></div>
                <p id="incoming-caller-name" class="mt-4 text-xl font-semibold"></p>
            </div>
            <div class="flex justify-center space-x-4">
                <button onclick="rejectCall()" class="px-6 py-3 bg-red-600 text-white rounded-full font-semibold hover:bg-red-700 transition-all">Reject</button>
                <button onclick="acceptCall()" class="px-6 py-3 bg-green-600 text-white rounded-full font-semibold hover:bg-green-700 transition-all">Accept</button>
            </div>
        </div>
    </div>
</div>

<div id="call-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm"></div>
        <div class="bg-slate-800 text-white rounded-2xl shadow-2xl w-full max-w-4xl h-[90vh] flex flex-col z-10 p-4">
            <div class="relative flex-1 bg-black rounded-lg overflow-hidden">
                <video id="remote-video" autoplay playsinline class="w-full h-full object-cover"></video>
                <video id="local-video" autoplay playsinline muted class="absolute bottom-4 right-4 w-1/4 max-w-[200px] rounded-lg border-2 border-slate-600 shadow-lg"></video>
            </div>
            <div class="flex justify-center items-center space-x-6 py-4">
                <button onclick="toggleMute()" id="mute-button" class="p-3 bg-slate-700/50 rounded-full hover:bg-slate-600 transition-colors" title="Mute">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.683 4.902 11 5.276 11 6v12c0 .724-.317 1.098-.707.707L5.586 15z"></path></svg>
                </button>
                <button onclick="toggleVideo()" id="video-button" class="p-3 bg-slate-700/50 rounded-full hover:bg-slate-600 transition-colors" title="Turn off camera">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                </button>
                <button onclick="endCall()" class="p-4 bg-red-600 rounded-full hover:bg-red-700 transition-transform transform hover:scale-110" title="End call">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM7.707 7.707a1 1 0 011.414 0L10 8.586l.879-.879a1 1 0 111.414 1.414L11.414 10l.879.879a1 1 0 01-1.414 1.414L10 11.414l-.879.879a1 1 0 01-1.414-1.414L8.586 10 7.707 9.121a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
        </div>
    </div>
</div>

