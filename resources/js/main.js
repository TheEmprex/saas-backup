// Main Vue entry point for testing

import { createApp } from 'vue';
import MessagingApp from './components/Messages/MessagingApp.vue';

const app = createApp({});

app.component('MessagingApp', MessagingApp);

app.mount('#messaging-app');
