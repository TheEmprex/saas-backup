// WebRTC implementation for audio/video calls
let localStream = null;
let remoteStream = null;
let peerConnection = null;
let isCallActive = false;
let isVideoCall = false;

// ICE servers configuration
const iceServers = {
    iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' }
    ]
};

// Initialize WebRTC
function initializeWebRTC() {
    console.log('WebRTC initialized');
}

// Start audio call
function startAudioCall() {
    console.log('Starting audio call...');
    isVideoCall = false;
    showCallModal();
    initiateCall(false);
}

// Start video call
function startVideoCall() {
    console.log('Starting video call...');
    isVideoCall = true;
    showCallModal();
    initiateCall(true);
}

// Show call modal
function showCallModal() {
    const modal = document.getElementById('call-modal');
    const videoContainer = document.getElementById('video-container');
    const audioContainer = document.getElementById('audio-container');
    
    if (modal) {
        modal.classList.remove('hidden');
        
        if (isVideoCall) {
            videoContainer.classList.remove('hidden');
            audioContainer.classList.add('hidden');
        } else {
            audioContainer.classList.remove('hidden');
            videoContainer.classList.add('hidden');
        }
    }
}

// Hide call modal
function hideCallModal() {
    const modal = document.getElementById('call-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// End call
function endCall() {
    console.log('Ending call...');
    isCallActive = false;
    
    if (localStream) {
        localStream.getTracks().forEach(track => track.stop());
        localStream = null;
    }
    
    if (peerConnection) {
        peerConnection.close();
        peerConnection = null;
    }
    
    hideCallModal();
}

// Initiate call with media constraints
async function initiateCall(includeVideo) {
    try {
        const constraints = {
            audio: true,
            video: includeVideo
        };
        
        localStream = await navigator.mediaDevices.getUserMedia(constraints);
        
        // Display local stream
        const localVideo = document.getElementById('local-video');
        if (localVideo && includeVideo) {
            localVideo.srcObject = localStream;
        }
        
        // Create peer connection
        peerConnection = new RTCPeerConnection(iceServers);
        
        // Add local stream to peer connection
        localStream.getTracks().forEach(track => {
            peerConnection.addTrack(track, localStream);
        });
        
        // Handle remote stream
        peerConnection.ontrack = (event) => {
            remoteStream = event.streams[0];
            const remoteVideo = document.getElementById('remote-video');
            if (remoteVideo) {
                remoteVideo.srcObject = remoteStream;
            }
        };
        
        // Handle ICE candidates
        peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                // Send ICE candidate to remote peer via signaling server
                sendSignalingMessage({
                    type: 'ice-candidate',
                    candidate: event.candidate
                });
            }
        };
        
        isCallActive = true;
        console.log('Call initiated successfully');
        
    } catch (error) {
        console.error('Error initiating call:', error);
        alert('Error accessing camera/microphone. Please check permissions.');
        hideCallModal();
    }
}

// Send signaling message (placeholder for actual signaling implementation)
function sendSignalingMessage(message) {
    console.log('Sending signaling message:', message);
    // This would integrate with your signaling server
    // For now, it's just a placeholder
}

// Handle incoming signaling messages
function handleSignalingMessage(message) {
    console.log('Received signaling message:', message);
    
    switch (message.type) {
        case 'offer':
            handleOffer(message.offer);
            break;
        case 'answer':
            handleAnswer(message.answer);
            break;
        case 'ice-candidate':
            handleIceCandidate(message.candidate);
            break;
    }
}

// Handle offer
async function handleOffer(offer) {
    try {
        await peerConnection.setRemoteDescription(offer);
        
        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);
        
        sendSignalingMessage({
            type: 'answer',
            answer: answer
        });
        
    } catch (error) {
        console.error('Error handling offer:', error);
    }
}

// Handle answer
async function handleAnswer(answer) {
    try {
        await peerConnection.setRemoteDescription(answer);
    } catch (error) {
        console.error('Error handling answer:', error);
    }
}

// Handle ICE candidate
async function handleIceCandidate(candidate) {
    try {
        await peerConnection.addIceCandidate(candidate);
    } catch (error) {
        console.error('Error handling ICE candidate:', error);
    }
}

// Mute/unmute audio
function toggleAudio() {
    if (localStream) {
        const audioTrack = localStream.getAudioTracks()[0];
        if (audioTrack) {
            audioTrack.enabled = !audioTrack.enabled;
            const muteButton = document.getElementById('mute-button');
            if (muteButton) {
                muteButton.textContent = audioTrack.enabled ? 'Mute' : 'Unmute';
            }
        }
    }
}

// Turn video on/off
function toggleVideo() {
    if (localStream && isVideoCall) {
        const videoTrack = localStream.getVideoTracks()[0];
        if (videoTrack) {
            videoTrack.enabled = !videoTrack.enabled;
            const videoButton = document.getElementById('video-button');
            if (videoButton) {
                videoButton.textContent = videoTrack.enabled ? 'Turn Off Video' : 'Turn On Video';
            }
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeWebRTC();
});
