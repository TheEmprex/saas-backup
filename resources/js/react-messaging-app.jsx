import React from 'react';
import { createRoot } from 'react-dom/client';
import MessagesApp from './Components/messaging/MessagesApp.jsx';

console.log('React messaging app script loaded');

// Mount the React messaging app
const messagingElement = document.getElementById('react-messaging-app');
console.log('Looking for element with ID: react-messaging-app');
console.log('Element found:', messagingElement);

if (messagingElement) {
    console.log('Mounting React messaging app...');
    try {
        const root = createRoot(messagingElement);
        root.render(<MessagesApp />);
        console.log('React messaging app mounted successfully');
    } catch (error) {
        console.error('Error mounting React messaging app:', error);
        console.error('Error stack:', error.stack);
    }
} else {
    console.error('Could not find element with ID: react-messaging-app');
    console.log('Available elements with IDs:', Array.from(document.querySelectorAll('[id]')).map(el => el.id));
}
