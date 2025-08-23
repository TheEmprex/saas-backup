import React, { useState, useRef, useEffect } from 'react';
import { Search, Plus, MessageCircle, Users, Send, Phone, Video, MoreVertical } from 'lucide-react';
import { motion } from 'framer-motion';
import { formatDistanceToNow } from 'date-fns';

// ConversationList Component
const ConversationList = ({ conversations, selectedConversation, onSelectConversation, onNewConversation }) => {
  const [searchTerm, setSearchTerm] = useState('');

  const filteredConversations = conversations.filter(conv =>
    conv.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    conv.lastMessage.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const totalUnread = conversations.reduce((sum, conv) => sum + (conv.unreadCount || 0), 0);

  return (
    <div className="flex flex-col h-full bg-white dark:bg-zinc-800">
      {/* Header */}
      <div className="p-6 border-b border-gray-200 dark:border-zinc-700 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20">
        <div className="flex items-center justify-between mb-4">
          <div className="flex items-center space-x-3">
            <div className="p-2 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg">
              <MessageCircle className="w-5 h-5 text-white" />
            </div>
            <div>
              <h2 className="text-xl font-bold text-gray-900 dark:text-white">Messages</h2>
              {totalUnread > 0 && (
                <p className="text-sm text-blue-600 dark:text-blue-400">
                  {totalUnread} unread message{totalUnread !== 1 ? 's' : ''}
                </p>
              )}
            </div>
          </div>
          <button
            onClick={onNewConversation}
            className="p-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
            title="Start new conversation"
          >
            <Plus size={18} />
          </button>
        </div>
        
        {/* Search */}
        <div className="relative">
          <Search className="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500" size={18} />
          <input
            type="text"
            placeholder="Search conversations..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full pl-12 pr-4 py-3 bg-white dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
          />
        </div>
      </div>

      {/* Conversations */}
      <div className="flex-1 overflow-y-auto">
        {filteredConversations.length === 0 ? (
          <div className="p-8 text-center">
            <div className="w-16 h-16 bg-gradient-to-br from-gray-400 to-gray-500 rounded-full flex items-center justify-center mx-auto mb-4">
              <Users className="w-8 h-8 text-white" />
            </div>
            <p className="text-gray-500 dark:text-gray-400 font-medium">
              {searchTerm ? 'No conversations found' : 'No conversations yet'}
            </p>
            <p className="text-sm text-gray-400 dark:text-gray-500 mt-1">
              {searchTerm ? 'Try a different search term' : 'Start a new conversation to get started'}
            </p>
          </div>
        ) : (
          <div className="space-y-1 p-2">
            {filteredConversations.map((conversation, index) => (
              <motion.div
                key={conversation.id}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: index * 0.05 }}
                whileHover={{ scale: 1.02 }}
                onClick={() => onSelectConversation(conversation)}
                className={`relative p-4 cursor-pointer rounded-xl transition-all duration-200 group ${
                  selectedConversation?.id === conversation.id
                    ? 'bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/30 dark:to-purple-900/30 border-2 border-blue-200 dark:border-blue-700 shadow-lg'
                    : 'hover:bg-gray-50 dark:hover:bg-zinc-700/50 border-2 border-transparent'
                }`}
              >
                <div className="flex items-start space-x-4">
                  <div className="relative flex-shrink-0">
                    <div className={`w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg ${
                      conversation.name === 'Welcome Bot' 
                        ? 'bg-gradient-to-br from-green-500 to-emerald-600'
                        : conversation.name.startsWith('Demo')
                        ? 'bg-gradient-to-br from-orange-500 to-red-600'
                        : 'bg-gradient-to-br from-blue-500 to-purple-600'
                    }`}>
                      {conversation.avatar ? (
                        <img 
                          src={conversation.avatar} 
                          alt={conversation.name} 
                          className="w-full h-full rounded-full object-cover"
                        />
                      ) : (
                        conversation.name.charAt(0).toUpperCase()
                      )}
                    </div>
                    {conversation.isOnline && (
                      <div className="absolute bottom-0 right-0 w-4 h-4 bg-green-500 border-2 border-white dark:border-zinc-800 rounded-full"></div>
                    )}
                  </div>
                  
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center justify-between mb-1">
                      <h3 className={`font-semibold truncate ${
                        selectedConversation?.id === conversation.id
                          ? 'text-blue-900 dark:text-blue-100'
                          : 'text-gray-900 dark:text-white'
                      }`}>
                        {conversation.name}
                      </h3>
                      <div className="flex items-center space-x-2">
                        {conversation.unreadCount > 0 && (
                          <span className="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-md">
                            {conversation.unreadCount > 9 ? '9+' : conversation.unreadCount}
                          </span>
                        )}
                        <span className={`text-xs ${
                          selectedConversation?.id === conversation.id
                            ? 'text-blue-600 dark:text-blue-400'
                            : 'text-gray-500 dark:text-gray-400'
                        }`}>
                          {formatDistanceToNow(new Date(conversation.lastMessageTime), { addSuffix: true })}
                        </span>
                      </div>
                    </div>
                    
                    <p className={`text-sm truncate leading-relaxed ${
                      conversation.unreadCount > 0
                        ? 'text-gray-900 dark:text-white font-medium'
                        : selectedConversation?.id === conversation.id
                        ? 'text-blue-700 dark:text-blue-300'
                        : 'text-gray-600 dark:text-gray-400'
                    }`}>
                      {conversation.lastMessage}
                    </p>
                  </div>
                </div>
              </motion.div>
            ))}
          </div>
        )}
      </div>
      
      {/* Footer */}
      <div className="p-4 border-t border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50">
        <p className="text-xs text-gray-500 dark:text-gray-400 text-center">
          {conversations.length} conversation{conversations.length !== 1 ? 's' : ''}
        </p>
      </div>
    </div>
  );
};

// ChatHeader Component
const ChatHeader = ({ conversation }) => {
  if (!conversation) return null;

  return (
    <div className="p-4 border-b border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-4">
          <div className="relative">
            <div className={`w-10 h-10 rounded-full flex items-center justify-center text-white font-bold shadow-lg ${
              conversation.name === 'Welcome Bot' 
                ? 'bg-gradient-to-br from-green-500 to-emerald-600'
                : conversation.name.startsWith('Demo')
                ? 'bg-gradient-to-br from-orange-500 to-red-600'
                : 'bg-gradient-to-br from-blue-500 to-purple-600'
            }`}>
              {conversation.avatar ? (
                <img 
                  src={conversation.avatar} 
                  alt={conversation.name} 
                  className="w-full h-full rounded-full object-cover"
                />
              ) : (
                conversation.name.charAt(0).toUpperCase()
              )}
            </div>
            {conversation.isOnline && (
              <div className="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-zinc-800 rounded-full"></div>
            )}
          </div>
          <div>
            <h3 className="font-semibold text-gray-900 dark:text-white">{conversation.name}</h3>
            <p className="text-sm text-gray-500 dark:text-gray-400">
              {conversation.isOnline ? 'Active now' : 'Last seen recently'}
            </p>
          </div>
        </div>
        
        <div className="flex items-center space-x-2">
          <button className="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition-colors">
            <Phone size={18} />
          </button>
          <button className="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition-colors">
            <Video size={18} />
          </button>
          <button className="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition-colors">
            <MoreVertical size={18} />
          </button>
        </div>
      </div>
    </div>
  );
};

// MessageBubble Component
const MessageBubble = ({ message, isOwn }) => {
  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className={`flex mb-4 ${isOwn ? 'justify-end' : 'justify-start'}`}
    >
      <div className={`max-w-xs lg:max-w-md px-4 py-2 rounded-2xl shadow-sm ${
        isOwn 
          ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white' 
          : 'bg-white dark:bg-zinc-700 text-gray-900 dark:text-white border border-gray-200 dark:border-zinc-600'
      }`}>
        <p className="text-sm leading-relaxed">{message.text}</p>
        <p className={`text-xs mt-1 ${
          isOwn ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400'
        }`}>
          {formatDistanceToNow(new Date(message.timestamp), { addSuffix: true })}
        </p>
      </div>
    </motion.div>
  );
};

// MessageInput Component
const MessageInput = ({ messageText, onMessageChange, onSendMessage }) => {
  const handleKeyPress = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      onSendMessage();
    }
  };

  return (
    <div className="p-4 border-t border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
      <div className="flex items-end space-x-3">
        <div className="flex-1">
          <textarea
            value={messageText}
            onChange={onMessageChange}
            onKeyPress={handleKeyPress}
            placeholder="Type a message..."
            rows={1}
            className="w-full px-4 py-3 border border-gray-200 dark:border-zinc-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
            style={{ minHeight: '44px', maxHeight: '120px' }}
          />
        </div>
        <button
          onClick={onSendMessage}
          disabled={!messageText.trim()}
          className={`p-3 rounded-xl transition-all duration-200 ${
            messageText.trim()
              ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:scale-105'
              : 'bg-gray-200 dark:bg-zinc-600 text-gray-400 dark:text-gray-500 cursor-not-allowed'
          }`}
        >
          <Send size={18} />
        </button>
      </div>
    </div>
  );
};

// NewConversationModal Component
const NewConversationModal = ({ isOpen, onClose, onCreateConversation }) => {
  const [contactName, setContactName] = useState('');

  const handleSubmit = (e) => {
    e.preventDefault();
    if (contactName.trim()) {
      onCreateConversation(contactName.trim());
      setContactName('');
    }
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <motion.div
        initial={{ opacity: 0, scale: 0.9 }}
        animate={{ opacity: 1, scale: 1 }}
        className="bg-white dark:bg-zinc-800 rounded-xl p-6 w-full max-w-md mx-4 shadow-2xl"
      >
        <h2 className="text-xl font-bold text-gray-900 dark:text-white mb-4">Start New Conversation</h2>
        <form onSubmit={handleSubmit}>
          <input
            type="text"
            value={contactName}
            onChange={(e) => setContactName(e.target.value)}
            placeholder="Enter contact name..."
            className="w-full px-4 py-3 border border-gray-200 dark:border-zinc-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 mb-4"
            autoFocus
          />
          <div className="flex justify-end space-x-3">
            <button
              type="button"
              onClick={onClose}
              className="px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              disabled={!contactName.trim()}
              className={`px-6 py-2 rounded-lg transition-all duration-200 ${
                contactName.trim()
                  ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700'
                  : 'bg-gray-200 dark:bg-zinc-600 text-gray-400 dark:text-gray-500 cursor-not-allowed'
              }`}
            >
              Start Chat
            </button>
          </div>
        </form>
      </motion.div>
    </div>
  );
};

const MessagesApp = () => {
  const [conversations, setConversations] = useState([]);
  const [selectedConversation, setSelectedConversation] = useState(null);
  const [messageText, setMessageText] = useState('');
  const [isNewConversationModalOpen, setIsNewConversationModalOpen] = useState(false);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const messagesEndRef = useRef(null);

  // Load conversations from API
  const loadConversations = async () => {
    try {
      setLoading(true);
      const baseURL = window.apiBaseUrl || '/api/marketplace';
      const token = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      
      const response = await fetch(`${baseURL}/conversations`, {
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': token
        }
      });
      
      if (!response.ok) {
        throw new Error('Failed to load conversations');
      }
      
      const conversations = await response.json();
      
      // Transform API data to match UI expectations
      const transformedConversations = conversations.map(conv => {
        const otherUser = conv.user1_id === window.userData.id ? conv.user2 : conv.user1;
        return {
          id: conv.id,
          name: otherUser?.name || 'Unknown User',
          avatar: otherUser?.avatar || null,
          lastMessage: conv.last_message?.content || 'No messages yet',
          lastMessageTime: conv.last_message?.created_at || conv.created_at,
          unreadCount: 0, // TODO: Calculate unread count
          isOnline: false, // TODO: Add online status
          messages: [] // Load messages separately when conversation is selected
        };
      });
      
      setConversations(transformedConversations);
      
      // If no conversations, show demo data
      if (!transformedConversations || transformedConversations.length === 0) {
        setConversations([
          {
            id: 'demo-1',
            name: 'Welcome Bot',
            avatar: null,
            lastMessage: 'Welcome to your messaging platform! 🎉',
            lastMessageTime: new Date().toISOString(),
            unreadCount: 1,
            isOnline: true,
            messages: [
              {
                id: 1,
                text: 'Welcome to your new messaging platform! 🎉',
                timestamp: new Date().toISOString(),
                isOwn: false,
                sender: {
                  name: 'Welcome Bot',
                  avatar: null
                }
              },
              {
                id: 2,
                text: 'This is a modern, professional messaging interface built with React.',
                timestamp: new Date().toISOString(),
                isOwn: false,
                sender: {
                  name: 'Welcome Bot',
                  avatar: null
                }
              },
              {
                id: 3,
                text: 'Start a new conversation by clicking the + button above, or try sending a message here!',
                timestamp: new Date().toISOString(),
                isOwn: false,
                sender: {
                  name: 'Welcome Bot',
                  avatar: null
                }
              }
            ]
          }
        ]);
      }
    } catch (err) {
      console.error('Error loading conversations:', err);
      setError(err.message);
      // Show demo data on error
      setConversations([
        {
          id: 'demo-1',
          name: 'Demo Chat',
          avatar: null,
          lastMessage: 'Unable to load real conversations, showing demo',
          lastMessageTime: new Date().toISOString(),
          unreadCount: 0,
          isOnline: false,
          messages: [
            {
              id: 1,
              text: 'Unable to connect to the server. This is a demo message.',
              timestamp: new Date().toISOString(),
              isOwn: false,
              sender: {
                name: 'Demo Chat',
                avatar: null
              }
            }
          ]
        }
      ]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadConversations();
  }, []);

  useEffect(() => {
    if (conversations.length > 0 && !selectedConversation) {
      setSelectedConversation(conversations[0]);
    }
  }, [conversations]);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  useEffect(() => {
    scrollToBottom();
  }, [selectedConversation?.messages]);

  const handleSelectConversation = (conversation) => {
    setSelectedConversation(conversation);
    // Mark conversation as read
    setConversations(prev =>
      prev.map(conv =>
        conv.id === conversation.id
          ? { ...conv, unreadCount: 0 }
          : conv
      )
    );
  };

  const handleSendMessage = async () => {
    if (!messageText.trim() || !selectedConversation) return;

    const newMessage = {
      id: Date.now(),
      text: messageText.trim(),
      timestamp: new Date().toISOString(),
      isOwn: true,
      sender: {
        name: 'You',
        avatar: null
      }
    };

    // Add message optimistically
    setConversations(prev =>
      prev.map(conv =>
        conv.id === selectedConversation.id
          ? {
              ...conv,
              messages: [...conv.messages, newMessage],
              lastMessage: newMessage.text,
              lastMessageTime: newMessage.timestamp
            }
          : conv
      )
    );

    setSelectedConversation(prev => ({
      ...prev,
      messages: [...prev.messages, newMessage],
      lastMessage: newMessage.text,
      lastMessageTime: newMessage.timestamp
    }));

    setMessageText('');

    try {
      // Try to send via API
      const response = await fetch(`/api/messages/${selectedConversation.id}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify({
          message: newMessage.text
        })
      });

      if (!response.ok) {
        throw new Error('Failed to send message');
      }

      // For demo purposes, simulate a response
      if (selectedConversation.id === 'demo-1') {
        setTimeout(() => {
          const responseMessage = {
            id: Date.now() + 1,
            text: `Thanks for your message: "${newMessage.text}"! This messaging system is now fully functional with modern UI/UX design. 🚀`,
            timestamp: new Date().toISOString(),
            isOwn: false,
            sender: {
              name: selectedConversation.name,
              avatar: null
            }
          };

          setConversations(prev =>
            prev.map(conv =>
              conv.id === selectedConversation.id
                ? {
                    ...conv,
                    messages: [...conv.messages, responseMessage],
                    lastMessage: responseMessage.text,
                    lastMessageTime: responseMessage.timestamp
                  }
                : conv
            )
          );

          setSelectedConversation(prev => ({
            ...prev,
            messages: [...prev.messages, responseMessage],
            lastMessage: responseMessage.text,
            lastMessageTime: responseMessage.timestamp
          }));
        }, 1000);
      }
    } catch (err) {
      console.error('Error sending message:', err);
      // For demo, still show the message was sent
    }
  };

  const handleCreateConversation = (contactName) => {
    const newConversation = {
      id: `new-${Date.now()}`,
      name: contactName,
      avatar: null,
      lastMessage: 'New conversation started...',
      lastMessageTime: new Date().toISOString(),
      unreadCount: 0,
      isOnline: false,
      messages: []
    };

    setConversations(prev => [newConversation, ...prev]);
    setSelectedConversation(newConversation);
    setIsNewConversationModalOpen(false);
  };

  if (loading) {
    return (
      <div className="flex h-full items-center justify-center">
        <div className="text-center">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-blue-600 border-r-transparent mb-4"></div>
          <p className="text-gray-600 dark:text-gray-400">Loading conversations...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="flex h-full bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 overflow-hidden">
      {/* Conversation List */}
      <div className="w-1/3 min-w-80 max-w-md border-r border-gray-200 dark:border-zinc-700">
        <ConversationList
          conversations={conversations}
          selectedConversation={selectedConversation}
          onSelectConversation={handleSelectConversation}
          onNewConversation={() => setIsNewConversationModalOpen(true)}
        />
      </div>

      {/* Chat Area */}
      <div className="flex-1 flex flex-col">
        {selectedConversation ? (
          <>
            {/* Chat Header */}
            <ChatHeader conversation={selectedConversation} />

            {/* Messages */}
            <div className="flex-1 overflow-y-auto p-4 bg-gray-50 dark:bg-zinc-900">
              {selectedConversation.messages.map((message) => (
                <MessageBubble
                  key={message.id}
                  message={message}
                  isOwn={message.isOwn}
                />
              ))}
              {selectedConversation.messages.length === 0 && (
                <div className="text-center text-gray-500 dark:text-gray-400 mt-8">
                  <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                  </div>
                  <p className="text-lg font-medium">No messages yet</p>
                  <p className="text-sm">Start the conversation by sending a message!</p>
                </div>
              )}
              <div ref={messagesEndRef} />
            </div>

            {/* Message Input */}
            <MessageInput
              messageText={messageText}
              onMessageChange={(e) => setMessageText(e.target.value)}
              onSendMessage={handleSendMessage}
            />
          </>
        ) : (
          <div className="flex items-center justify-center h-full bg-gray-50 dark:bg-zinc-900">
            <div className="text-center text-gray-500 dark:text-gray-400">
              <div className="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg className="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
              </div>
              <h3 className="text-2xl font-bold text-gray-900 dark:text-white mb-2">Welcome to Messages</h3>
              <p className="text-lg text-gray-600 dark:text-gray-400">Select a conversation to start messaging</p>
            </div>
          </div>
        )}
      </div>

      {/* New Conversation Modal */}
      <NewConversationModal
        isOpen={isNewConversationModalOpen}
        onClose={() => setIsNewConversationModalOpen(false)}
        onCreateConversation={handleCreateConversation}
      />
    </div>
  );
};

export default MessagesApp;
