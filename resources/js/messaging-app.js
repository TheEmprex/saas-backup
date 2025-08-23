import { createApp } from 'vue'
import MessagingApp from './Components/MessagingApp.vue'

// Create Vue app
const app = createApp(MessagingApp)

// Mount the app
app.mount('#vue-messaging-app')

console.log('Vue messaging app loaded')
