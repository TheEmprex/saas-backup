# Enhanced Messaging System Documentation

## Overview

The Enhanced Messaging System is a comprehensive, real-time messaging solution built for our SaaS application. It provides advanced features including conversations, real-time messaging, file sharing, user management, and system monitoring.

## Architecture

### Backend Components

#### API Controllers (Laravel)
- **ConversationController**: Handles conversation management
- **MessageController**: Manages message operations
- **UserController**: User search and profile management  
- **SystemController**: Admin monitoring and system health

#### Database Schema
- `conversations`: Main conversation records
- `messages`: Individual messages with rich content support
- `conversation_user`: Many-to-many relationship for participants
- `message_reads`: Read receipts tracking
- `message_reactions`: Emoji reactions to messages

#### Real-time Features
- WebSocket connections via Laravel Echo
- Typing indicators
- Online status tracking
- Live message delivery
- Push notifications

### Frontend Components (Vue.js)

#### Core Components
- **EnhancedMessagingApp**: Main application container
- **ConversationList**: List of user conversations
- **MessagesList**: Message display and interaction
- **UserSearch**: User discovery and selection
- **MessageComposer**: Rich message input with file support

#### State Management (Pinia)
- Centralized state for conversations, messages, and users
- Real-time synchronization
- Offline support and data persistence

## API Endpoints

### Conversations API (`/api/marketplace/v1/conversations`)

#### List Conversations
```
GET /conversations
Query Parameters:
- per_page: Number of conversations per page (default: 15)
- type: Filter by conversation type (direct, group)
- archived: Include archived conversations
```

#### Create Conversation
```
POST /conversations
Body:
{
  "type": "direct|group",
  "name": "Group Name" (required for groups),
  "participants": [user_ids]
}
```

#### Get Conversation Details
```
GET /conversations/{id}
```

#### Archive/Unarchive Conversation
```
POST /conversations/{id}/archive
POST /conversations/{id}/unarchive
```

#### Mute/Unmute Conversation
```
POST /conversations/{id}/mute
POST /conversations/{id}/unmute
```

#### Star/Unstar Conversation
```
POST /conversations/{id}/star
POST /conversations/{id}/unstar
```

#### Leave Conversation
```
DELETE /conversations/{id}
```

#### Get Participants
```
GET /conversations/{id}/participants
```

#### Update Typing Status
```
POST /conversations/{id}/typing
Body: { "typing": true|false }
```

### Messages API (`/api/marketplace/v1/messages`)

#### Get Messages
```
GET /conversations/{id}/messages
Query Parameters:
- per_page: Messages per page (default: 50)
- before: Get messages before specific message ID
```

#### Send Message
```
POST /conversations/{id}/messages
Body:
{
  "content": "Message content",
  "type": "text|image|file|voice",
  "reply_to_id": "message_id" (optional)
}
```

#### Upload File
```
POST /messages/upload
Body (multipart/form-data):
- file: File to upload
- conversation_id: Target conversation
- type: File type (image, file, voice)
```

#### Edit Message
```
PUT /messages/{id}
Body: { "content": "Updated content" }
```

#### Delete Message
```
DELETE /messages/{id}
```

#### Add Reaction
```
POST /messages/{id}/reactions
Body: { "emoji": "👍" }
```

#### Remove Reaction
```
DELETE /messages/{id}/reactions/{emoji}
```

#### Mark as Read
```
POST /messages/mark-read
Body: {
  "conversation_id": "conversation_id",
  "message_id": "latest_message_id"
}
```

#### Search Messages
```
GET /messages/search
Query Parameters:
- q: Search query
- conversation_id: Limit to specific conversation
- type: Filter by message type
- date_from/date_to: Date range filter
```

#### Forward Message
```
POST /messages/{id}/forward
Body: { "conversation_ids": [conversation_ids] }
```

### Users API (`/api/marketplace/v1/users`)

#### Search Users
```
GET /users/search
Query Parameters:
- q: Search query
- type: Filter by user type
- limit: Number of results (default: 20)
```

#### Get User Profile
```
GET /users/{id}
```

#### Get Online Users
```
GET /users/online
```

#### Update Online Status
```
POST /users/online-status
Body: { "status": "online|away|offline" }
```

#### Get Recent Activity
```
GET /users/{id}/activity
```

### System Monitoring API (`/api/marketplace/v1/system`) [Admin Only]

#### System Health Check
```
GET /system/health
```

#### System Metrics
```
GET /system/metrics
```

#### System Logs
```
GET /system/logs
Query Parameters:
- level: Filter by log level
- date_from/date_to: Date range
- per_page: Results per page
```

#### Export Logs
```
POST /system/logs/export
Body: {
  "format": "json|csv",
  "level": "info|warning|error",
  "date_from": "YYYY-MM-DD",
  "date_to": "YYYY-MM-DD"
}
```

#### Clean Old Logs
```
DELETE /system/logs/clean
Body: { "days": 30 }
```

#### Live Events
```
GET /system/events
Query Parameters:
- type: Filter by event type
- user_id: Filter by user
```

## Frontend Usage

### Basic Setup

```javascript
// In your main application file
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import EnhancedMessagingApp from '@/Components/EnhancedMessagingApp.vue'

const app = createApp({
  components: { EnhancedMessagingApp }
})

app.use(createPinia())
app.mount('#app')
```

### Component Usage

```vue
<template>
  <div class="messaging-container">
    <enhanced-messaging-app 
      :user="currentUser"
      :initial-conversation="conversationId"
      @message-sent="handleMessageSent"
      @conversation-created="handleConversationCreated"
    />
  </div>
</template>
```

### Store Usage

```javascript
import { useMessagingStore } from '@/stores/messaging'

export default {
  setup() {
    const messagingStore = useMessagingStore()
    
    // Load conversations
    messagingStore.loadConversations()
    
    // Send message
    const sendMessage = async (conversationId, content) => {
      await messagingStore.sendMessage(conversationId, {
        content,
        type: 'text'
      })
    }
    
    return { messagingStore, sendMessage }
  }
}
```

## Real-time Features

### WebSocket Events

The system broadcasts the following events:

#### Message Events
- `message.sent`: New message in conversation
- `message.edited`: Message was edited
- `message.deleted`: Message was deleted
- `message.reaction.added`: Reaction added to message
- `message.reaction.removed`: Reaction removed from message

#### Conversation Events
- `conversation.updated`: Conversation settings changed
- `conversation.participant.joined`: User joined conversation
- `conversation.participant.left`: User left conversation

#### User Events
- `user.typing`: User started/stopped typing
- `user.online`: User online status changed
- `user.offline`: User went offline

#### System Events
- `system.maintenance`: System maintenance notification
- `system.alert`: System-wide alerts

### Listening to Events

```javascript
// Frontend event listening
Echo.private(`conversation.${conversationId}`)
  .listen('MessageSent', (event) => {
    messagingStore.addMessage(event.message)
  })
  .listen('UserTyping', (event) => {
    messagingStore.updateTypingStatus(event.user_id, event.typing)
  })
```

## Security

### Authentication
- All API endpoints require authentication via Sanctum tokens
- WebSocket connections are authenticated through Laravel Echo

### Authorization
- Users can only access conversations they are participants in
- Message editing/deletion limited to message author
- Admin endpoints restricted to admin users
- File uploads are validated and scanned

### Rate Limiting
- API endpoints have rate limiting to prevent abuse
- Real-time events are throttled to prevent flooding

### Data Validation
- All inputs are validated on both client and server side
- File uploads have size and type restrictions
- XSS protection on all user content

## Performance Optimizations

### Caching
- Conversation lists are cached for 15 minutes
- User search results cached for 5 minutes
- System metrics cached for 1 minute
- Database queries optimized with proper indexing

### Pagination
- Messages are paginated (50 per page)
- Conversations are paginated (15 per page)
- Infinite scrolling for better UX

### Database Optimizations
- Proper indexing on frequently queried columns
- Soft deletes for messages to maintain conversation history
- Efficient eager loading to prevent N+1 queries

### Frontend Optimizations
- Virtual scrolling for large message lists
- Image lazy loading
- Component-level caching
- Optimistic UI updates

## Testing

### Running Tests

```bash
# Run all messaging tests
./scripts/test-messaging-system.sh

# Run only unit tests
./scripts/test-messaging-system.sh --unit-only

# Run only feature tests
./scripts/test-messaging-system.sh --feature-only

# Run specific test class
vendor/bin/phpunit tests/Feature/Api/V1/MessagingSystemTest.php
```

### Test Coverage

The test suite includes:
- Unit tests for all controller methods
- Feature tests for API endpoints
- Integration tests for real-time features
- Performance tests for critical paths

### Test Types

#### Unit Tests
- Controller method testing
- Model relationship testing
- Service class testing
- Utility function testing

#### Feature Tests
- End-to-end API testing
- Authentication and authorization testing
- File upload testing
- Error handling testing

#### Integration Tests
- WebSocket event testing
- Database transaction testing
- Cache behavior testing
- Queue job testing

## Monitoring and Logging

### System Health Monitoring
- Database connectivity checks
- Cache system status
- Queue system status
- Storage system status
- Memory and disk usage tracking

### Application Metrics
- User activity metrics
- Message volume metrics
- Conversation statistics
- Performance metrics
- Error rate tracking

### Logging
- All API requests logged
- Error logging with context
- Performance logging for slow queries
- Security event logging
- User activity logging

## Deployment

### Environment Requirements
- PHP 8.1+
- Laravel 10+
- Redis for caching and queues
- MySQL/PostgreSQL database
- WebSocket server (Laravel Echo Server)

### Configuration

```bash
# Environment variables
BROADCAST_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Real-time configuration
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster

# File upload configuration
FILESYSTEM_DISK=public
MAX_UPLOAD_SIZE=10240  # 10MB
```

### Deployment Steps

1. **Install Dependencies**
   ```bash
   composer install --no-dev
   npm ci && npm run build
   ```

2. **Run Migrations**
   ```bash
   php artisan migrate --force
   ```

3. **Seed Database** (optional)
   ```bash
   php artisan db:seed --class=MessagingSystemSeeder
   ```

4. **Configure WebSocket Server**
   ```bash
   laravel-echo-server init
   laravel-echo-server start
   ```

5. **Start Queue Workers**
   ```bash
   php artisan queue:work --daemon
   ```

6. **Set File Permissions**
   ```bash
   chmod -R 755 storage/
   chmod -R 755 bootstrap/cache/
   ```

## Troubleshooting

### Common Issues

#### WebSocket Connection Failed
- Check Laravel Echo Server is running
- Verify CORS configuration
- Check firewall settings for WebSocket port

#### Messages Not Updating Real-time
- Verify queue workers are running
- Check Redis connection
- Verify broadcast events are firing

#### File Upload Issues
- Check file size limits
- Verify storage permissions
- Check available disk space

#### Performance Issues
- Check database query performance
- Verify cache is working
- Monitor memory usage

### Debug Mode

Enable debug logging for detailed troubleshooting:

```bash
# In .env file
LOG_LEVEL=debug
MESSAGING_DEBUG=true
BROADCAST_DEBUG=true
```

## API Rate Limits

| Endpoint Group | Rate Limit | Window |
|---|---|---|
| Authentication | 5/minute | Per IP |
| Messaging | 60/minute | Per User |
| File Upload | 10/minute | Per User |
| Search | 30/minute | Per User |
| System Admin | 100/minute | Per User |

## File Upload Limits

| File Type | Max Size | Allowed Extensions |
|---|---|---|
| Images | 5MB | jpg, jpeg, png, gif, webp |
| Documents | 10MB | pdf, doc, docx, txt, rtf |
| Audio | 25MB | mp3, wav, m4a, ogg |
| Video | 100MB | mp4, avi, mov, wmv |

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile Safari 14+
- Chrome Mobile 90+

## Contributing

When contributing to the enhanced messaging system:

1. Run the full test suite before submitting changes
2. Follow the established coding standards
3. Update documentation for new features
4. Include tests for new functionality
5. Consider performance implications of changes

## Support

For support and questions:
- Check the troubleshooting section first
- Review system logs for errors
- Run the health check endpoint
- Contact the development team with specific error messages and context

---

*This documentation covers the Enhanced Messaging System v2.0. For updates and changes, refer to the changelog and version control history.*
