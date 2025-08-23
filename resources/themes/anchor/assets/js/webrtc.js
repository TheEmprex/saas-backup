// WebRTC implementation for audio/video calls
class WebRTCManager {
    constructor() {
        this.localStream = null;
        this.remoteStream = null;
        this.peerConnection = null;
        this.isCallActive = false;
        this.isMuted = false;
        this.isVideoOff = false;
        this.currentCall = null;
        
        // WebRTC configuration with STUN servers
        this.configuration = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' },
                { urls: 'stun:stun2.l.google.com:19302' }
            ]
        };
        
        this.init();
    }
    
    init() {
        // Check for browser support
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            console.error('WebRTC not supported in this browser');
            return;
        }
        
        // Only set up polling after a delay to ensure page is fully loaded
        setTimeout(() => {
            // Set up periodic polling for incoming calls
            setInterval(() => this.checkIncomingCalls(), 2000);
        }, 5000); // Wait 5 seconds before starting to poll
    }
    
    async startAudioCall() {
        try {
            await this.startCall('audio');
        } catch (error) {
            console.error('Error starting audio call:', error);
            alert('Could not start audio call. Please check your microphone permissions.');
        }
    }
    
    async startVideoCall() {
        try {
            await this.startCall('video');
        } catch (error) {
            console.error('Error starting video call:', error);
            alert('Could not start video call. Please check your camera and microphone permissions.');
        }
    }
    
    async startCall(type = 'audio') {
        if (this.isCallActive) {
            alert('A call is already in progress');
            return;
        }
        
        try {
            // Get user media
            const constraints = {
                audio: true,
                video: type === 'video'
            };
            
            this.localStream = await navigator.mediaDevices.getUserMedia(constraints);
            
            // Show call modal
            this.showCallModal(type);
            
            // Set up local video if video call
            if (type === 'video') {
                const localVideo = document.getElementById('local-video');
                if (localVideo) {
                    localVideo.srcObject = this.localStream;
                }
            }
            
            // Create peer connection
            this.peerConnection = new RTCPeerConnection(this.configuration);
            
            // Add local stream to peer connection
            this.localStream.getTracks().forEach(track => {
                this.peerConnection.addTrack(track, this.localStream);
            });
            
            // Handle remote stream
            this.peerConnection.ontrack = (event) => {
                const remoteVideo = document.getElementById('remote-video');
                if (remoteVideo && event.streams[0]) {
                    remoteVideo.srcObject = event.streams[0];
                }
            };
            
            // Handle ICE candidates
            this.peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    this.sendSignal({
                        type: 'ice-candidate',
                        candidate: event.candidate
                    });
                }
            };
            
            // Create offer
            const offer = await this.peerConnection.createOffer();
            await this.peerConnection.setLocalDescription(offer);
            
            // Send offer to server
            await this.sendSignal({
                type: 'offer',
                offer: offer,
                callType: type
            });
            
            this.isCallActive = true;
            this.currentCall = { type, status: 'outgoing' };
            
        } catch (error) {
            console.error('Error starting call:', error);
            this.endCall();
            throw error;
        }
    }
    
    async handleIncomingCall(callData) {
        if (this.isCallActive) {
            // Send busy signal
            await this.sendSignal({
                type: 'busy',
                callId: callData.id
            });
            return;
        }
        
        // Show incoming call notification
        const accept = confirm(`Incoming ${callData.callType} call. Accept?`);
        
        if (accept) {
            await this.acceptCall(callData);
        } else {
            await this.rejectCall(callData);
        }
    }
    
    async acceptCall(callData) {
        try {
            // Get user media
            const constraints = {
                audio: true,
                video: callData.callType === 'video'
            };
            
            this.localStream = await navigator.mediaDevices.getUserMedia(constraints);
            
            // Show call modal
            this.showCallModal(callData.callType);
            
            // Set up local video if video call
            if (callData.callType === 'video') {
                const localVideo = document.getElementById('local-video');
                if (localVideo) {
                    localVideo.srcObject = this.localStream;
                }
            }
            
            // Create peer connection
            this.peerConnection = new RTCPeerConnection(this.configuration);
            
            // Add local stream
            this.localStream.getTracks().forEach(track => {
                this.peerConnection.addTrack(track, this.localStream);
            });
            
            // Handle remote stream
            this.peerConnection.ontrack = (event) => {
                const remoteVideo = document.getElementById('remote-video');
                if (remoteVideo && event.streams[0]) {
                    remoteVideo.srcObject = event.streams[0];
                }
            };
            
            // Handle ICE candidates
            this.peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    this.sendSignal({
                        type: 'ice-candidate',
                        candidate: event.candidate,
                        callId: callData.id
                    });
                }
            };
            
            // Set remote description
            await this.peerConnection.setRemoteDescription(new RTCSessionDescription(callData.offer));
            
            // Create answer
            const answer = await this.peerConnection.createAnswer();
            await this.peerConnection.setLocalDescription(answer);
            
            // Send answer
            await this.sendSignal({
                type: 'answer',
                answer: answer,
                callId: callData.id
            });
            
            this.isCallActive = true;
            this.currentCall = { type: callData.callType, status: 'incoming', id: callData.id };
            
        } catch (error) {
            console.error('Error accepting call:', error);
            this.endCall();
        }
    }
    
    async rejectCall(callData) {
        await this.sendSignal({
            type: 'reject',
            callId: callData.id
        });
    }
    
    async endCall() {
        try {
            // Send end call signal
            if (this.currentCall) {
                await this.sendSignal({
                    type: 'end-call',
                    callId: this.currentCall.id
                });
            }
            
            // Stop local stream
            if (this.localStream) {
                this.localStream.getTracks().forEach(track => track.stop());
                this.localStream = null;
            }
            
            // Close peer connection
            if (this.peerConnection) {
                this.peerConnection.close();
                this.peerConnection = null;
            }
            
            // Hide call modal
            this.hideCallModal();
            
            // Reset state
            this.isCallActive = false;
            this.isMuted = false;
            this.isVideoOff = false;
            this.currentCall = null;
            
        } catch (error) {
            console.error('Error ending call:', error);
        }
    }
    
    toggleAudio() {
        if (this.localStream) {
            const audioTrack = this.localStream.getAudioTracks()[0];
            if (audioTrack) {
                audioTrack.enabled = !audioTrack.enabled;
                this.isMuted = !audioTrack.enabled;
                
                // Update button state
                const muteButton = document.getElementById('mute-button');
                if (muteButton) {
                    muteButton.classList.toggle('bg-red-500', this.isMuted);
                    muteButton.classList.toggle('bg-slate-500', !this.isMuted);
                }
            }
        }
    }
    
    toggleVideo() {
        if (this.localStream) {
            const videoTrack = this.localStream.getVideoTracks()[0];
            if (videoTrack) {
                videoTrack.enabled = !videoTrack.enabled;
                this.isVideoOff = !videoTrack.enabled;
                
                // Update button state
                const videoButton = document.getElementById('video-button');
                if (videoButton) {
                    videoButton.classList.toggle('bg-red-500', this.isVideoOff);
                    videoButton.classList.toggle('bg-slate-500', !this.isVideoOff);
                }
            }
        }
    }
    
    showCallModal(type) {
        const modal = document.getElementById('call-modal');
        const videoContainer = document.getElementById('video-container');
        const audioContainer = document.getElementById('audio-container');
        
        if (modal) {
            modal.classList.remove('hidden');
            
            if (type === 'video') {
                videoContainer.classList.remove('hidden');
                audioContainer.classList.add('hidden');
            } else {
                audioContainer.classList.remove('hidden');
                videoContainer.classList.add('hidden');
            }
        }
    }
    
    hideCallModal() {
        const modal = document.getElementById('call-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
    
    async sendSignal(signal) {
        try {
            const response = await fetch('/api/webrtc/signal', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    signal: signal,
                    to_user_id: window.contactUserId || null
                })
            });
            
            if (!response.ok) {
                throw new Error('Failed to send signal');
            }
            
        } catch (error) {
            console.error('Error sending signal:', error);
        }
    }
    
    async checkIncomingCalls() {
        if (this.isCallActive) return;
        
        // Don't check for calls if we're on login/register pages or if user is not authenticated
        const currentPath = window.location.pathname;
        if (currentPath.includes('/login') || currentPath.includes('/register') || 
            currentPath === '/' || currentPath.includes('/auth/')) {
            return;
        }
        
        // Only check for calls on message pages to avoid interference
        if (!currentPath.includes('/messages')) {
            return;
        }
        
        // Ensure we have a CSRF token before making the request
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            return;
        }
        
        try {
            const response = await fetch('/api/webrtc/incoming-calls', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.calls && data.calls.length > 0) {
                    // Handle the first incoming call
                    await this.handleIncomingCall(data.calls[0]);
                }
            }
            
        } catch (error) {
            console.error('Error checking incoming calls:', error);
            // Don't redirect on error
        }
    }
}

// Global WebRTC manager instance
let webrtcManager = null;

// Initialize WebRTC when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    webrtcManager = new WebRTCManager();
    
    // Get contact user ID from the page
    const contactElement = document.querySelector('[data-contact-id]');
    if (contactElement) {
        window.contactUserId = contactElement.dataset.contactId;
    }
});

// Global functions for buttons
function startAudioCall() {
    if (webrtcManager) {
        webrtcManager.startAudioCall();
    }
}

function startVideoCall() {
    if (webrtcManager) {
        webrtcManager.startVideoCall();
    }
}

function toggleAudio() {
    if (webrtcManager) {
        webrtcManager.toggleAudio();
    }
}

function toggleVideo() {
    if (webrtcManager) {
        webrtcManager.toggleVideo();
    }
}

function endCall() {
    if (webrtcManager) {
        webrtcManager.endCall();
    }
}

