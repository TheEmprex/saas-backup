
import React, { useState, useEffect, useRef, useCallback } from "react";
import { Conversation, Message, Contact, Folder, UploadFile } from "../../services/messagingApi";

import ConversationList from "./ConversationList.jsx";
import ChatHeader from "./ChatHeader.jsx";
import MessageBubble from "./MessageBubble.jsx";
import MessageInput from "./MessageInput.jsx";
import EmptyState from "./EmptyState.jsx";
import ProfileSidebar from "./ProfileSidebar.jsx";
import TypingIndicator from "./TypingIndicator.jsx";
import NewConversationModal from "./NewConversationModal.jsx";
import MoveToFolderModal from "./MoveToFolderModal.jsx";

/**
 * MAIN MESSAGES PAGE COMPONENT
 * 
 * This is the core messaging interface that handles:
 * - Real-time message updates (simulated with polling)
 * - Conversation management and switching
 * - File uploads and media sharing
 * - User profiles and contact management
 * - Folder organization system
 * 
 * CUSTOMIZATION TIPS:
 * - Change POLLING_INTERVAL to adjust real-time update frequency
 * - Modify the color scheme in Layout.js CSS variables
 * - Add your own integrations by replacing the placeholder functions
 */

export default function MessagesPage() {
  // For debugging - show a simple test first
  const [testMode, setTestMode] = useState(true);
  
  if (testMode) {
    return (
      <div className="p-8">
        <h1 className="text-2xl font-bold mb-4">Messages App Test</h1>
        <p className="mb-4">React is working! The component is loading correctly.</p>
        <button 
          onClick={() => setTestMode(false)}
          className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Load Full App
        </button>
      </div>
    );
  }
  
  // ===== STATE MANAGEMENT =====
  // Core data states
  const [conversations, setConversations] = useState([]);
  const [messages, setMessages] = useState([]);
  const [contacts, setContacts] = useState([]);
  const [folders, setFolders] = useState([]);
  
  // UI states
  const [selectedConversation, setSelectedConversation] = useState(null);
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedFolder, setSelectedFolder] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  
  // Advanced features states
  const [isProfileOpen, setIsProfileOpen] = useState(false);
  const [profileContact, setProfileContact] = useState(null);
  const [isTyping, setIsTyping] = useState(false);
  const [isSending, setIsSending] = useState(false);
  const [isNewConvoModalOpen, setIsNewConvoModalOpen] = useState(false);
  const [isMoveToFolderModalOpen, setIsMoveToFolderModalOpen] = useState(false);
  
  // References
  const messagesEndRef = useRef(null);
  
  // Configuration - adjust these to fit your needs
  const POLLING_INTERVAL = 5000; // 5 seconds - how often to refresh data
  const CURRENT_USER_ID = "current-user"; // Replace with your actual user ID system

  // ===== LIFECYCLE EFFECTS =====
  
  /**
   * Initial data loading and real-time polling setup
   * This simulates real-time updates by periodically refreshing data
   */
  useEffect(() => {
    loadData();

    // Set up polling for real-time simulation
    const interval = setInterval(() => {
      loadData(false); // Refresh without showing loader
    }, POLLING_INTERVAL);

    // Cleanup interval on component unmount
    return () => clearInterval(interval);
  }, []);

  /**
   * Load messages when conversation changes
   */
  useEffect(() => {
    if (selectedConversation) {
      loadMessages(selectedConversation.id, false);
    } else {
      setMessages([]);
    }
  }, [selectedConversation]);

  /**
   * Auto-scroll to bottom when new messages arrive
   */
  useEffect(() => {
    scrollToBottom();
  }, [messages]);

  // ===== DATA LOADING FUNCTIONS =====

  /**
   * Loads all necessary data for the messaging interface
   * @param {boolean} showLoader - Whether to display the main loading state
   */
  const loadData = async (showLoader = true) => {
    if (showLoader) setIsLoading(true);
    
    try {
      // Fetch all data in parallel for better performance
      const [conversationsData, contactsData, foldersData] = await Promise.all([
        Conversation.list("-last_message_time"),
        Contact.list("-created_date"),
        Folder.list("order_index")
      ]);
      
      setConversations(conversationsData);
      setContacts(contactsData);
      setFolders(foldersData);
      
      // If a conversation is selected, refresh its messages
      if (selectedConversation) {
        loadMessages(selectedConversation.id, false);
      }
    } catch (error) {
      console.error("Error loading data:", error);
      // In a real app, you might show a toast notification here
    }
    
    if (showLoader) setIsLoading(false);
  };

  /**
   * Loads messages for a specific conversation
   * @param {string} conversationId - The ID of the conversation
   * @param {boolean} showLoader - Whether to display a loading state
   */
  const loadMessages = async (conversationId, showLoader = true) => {
    try {
      const messagesData = await Message.filter(
        { conversation_id: conversationId }, 
        "created_date"
      );
      setMessages(messagesData);
      
      // Mark messages as read by the current user
      const unreadMessages = messagesData.filter(msg => !msg.is_read && msg.sender_id !== CURRENT_USER_ID);
      for (const msg of unreadMessages) {
        await Message.update(msg.id, { is_read: true });
      }
    } catch (error) {
      console.error("Error loading messages:", error);
    }
  };

  // ===== UI HELPER FUNCTIONS =====

  /**
   * Scrolls the chat to the bottom (latest messages)
   */
  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
  };

  // ===== MESSAGE HANDLING FUNCTIONS =====

  /**
   * Handles sending a new message
   * @param {string} content - The text content of the message
   * @param {string} type - The type of the message (e.g., "text", "image")
   * @param {object|null} fileData - Data for file attachments
   */
  const handleSendMessage = async (content, type = "text", fileData = null) => {
    if (!selectedConversation || (!content.trim() && !fileData)) return;

    setIsSending(true);
    try {
      // Create the message data object
      const messageData = {
        conversation_id: selectedConversation.id,
        sender_id: CURRENT_USER_ID,
        content: content.trim(),
        message_type: type,
        ...fileData // Spread file data if present
      };

      // Create the message in the database
      const newMessage = await Message.create(messageData);
      
      // Immediately add to local state for instant feedback
      setMessages(prev => [...prev, newMessage]);

      // Update conversation's last message info
      await Conversation.update(selectedConversation.id, {
        last_message_id: newMessage.id,
        last_message_text: type === "text" ? content : `Sent a ${type}`,
        last_message_time: new Date().toISOString()
      });

      // Refresh conversations list to show the updated last message
      await loadData(false);
    } catch (error) {
      console.error("Error sending message:", error);
      // In a real app, you might show an error notification here
    } finally {
      setIsSending(false);
    }
  };

  /**
   * Handles file uploads and creates appropriate message types
   * @param {File} file - The file to upload
   */
  const handleFileUpload = async (file) => {
    setIsSending(true);
    try {
      // Upload file to storage
      const { file_url } = await UploadFile({ file });
      
      // Determine message type based on file type
      const fileType = file.type.startsWith('image/') ? 'image' : 
                      file.type.startsWith('video/') ? 'video' : 'file';
      
      // Send message with file data
      await handleSendMessage("", fileType, {
        file_url,
        file_name: file.name,
        file_size: file.size
      });
    } catch (error) {
      console.error("Error uploading file:", error);
    } finally {
      setIsSending(false);
    }
  };

  /**
   * Simulates typing indicator functionality
   */
  const handleTyping = () => {
    if (!selectedConversation) return;
    
    // Show typing indicator
    setIsTyping(true);
    
    // Hide after 3 seconds
    setTimeout(() => setIsTyping(false), 3000);
  };
  
  /**
   * Handles opening the profile sidebar for a contact
   * @param {string} contactId - The ID of the contact to display
   */
  const handleProfileClick = (contactId) => {
    const contact = contacts.find(c => c.id === contactId);
    if (contact) {
      setProfileContact(contact);
      setIsProfileOpen(true);
    }
  };

  /**
   * Handles adding emoji reactions to messages
   * @param {string} messageId - The ID of the message being reacted to
   * @param {string} emoji - The emoji being used for the reaction
   */
  const handleReaction = async (messageId, emoji) => {
    const message = messages.find(m => m.id === messageId);
    if (!message) return;

    try {
      // Get existing reactions or create empty array
      const updatedReactions = message.reactions ? [...message.reactions] : [];
      const reactionIndex = updatedReactions.findIndex(r => r.emoji === emoji);

      if (reactionIndex > -1) {
        // Reaction exists - toggle user's reaction
        const reaction = updatedReactions[reactionIndex];
        const userIndex = reaction.user_ids.indexOf(CURRENT_USER_ID);
        
        if (userIndex > -1) {
          // Remove user's reaction
          reaction.user_ids.splice(userIndex, 1);
          if (reaction.user_ids.length === 0) {
            updatedReactions.splice(reactionIndex, 1);
          }
        } else {
          // Add user's reaction
          reaction.user_ids.push(CURRENT_USER_ID);
        }
      } else {
        // New reaction
        updatedReactions.push({ emoji, user_ids: [CURRENT_USER_ID] });
      }

      // Update message in database
      await Message.update(messageId, { reactions: updatedReactions });
      
      // Update local state immediately for instant feedback
      setMessages(prevMessages => 
        prevMessages.map(m => 
          m.id === messageId ? { ...m, reactions: updatedReactions } : m
        )
      );
    } catch (error) {
      console.error("Error adding reaction:", error);
    }
  };

  const handleDeleteMessage = async (messageId) => {
    try {
      await Message.delete(messageId);
      // Remove message from local state for instant feedback
      setMessages(prevMessages => prevMessages.filter(m => m.id !== messageId));
      // You might want to update the conversation's last message if the deleted one was the last
      await loadData(false);
    } catch (error) {
      console.error("Error deleting message:", error);
    }
  };

  const handleStartCall = async (type) => {
    if (!selectedConversation) return;

    const callType = type === 'video' ? 'Video Call' : 'Voice Call';
    await handleSendMessage(`${callType} initiated`, 'call', {
      content: `${callType} initiated`,
    });
  };

  // ===== CONVERSATION & FOLDER MANAGEMENT =====

  const handleCreateConversation = async (participantIds, groupName) => {
    try {
      const isGroup = participantIds.length > 1;
      const conversationData = {
        participants: [CURRENT_USER_ID, ...participantIds],
        conversation_type: isGroup ? 'group' : 'direct',
        title: isGroup ? groupName : null,
      };

      const newConversation = await Conversation.create(conversationData);
      await loadData(false);
      setSelectedConversation(newConversation);
      setIsNewConvoModalOpen(false);
    } catch (error) {
      console.error("Error creating conversation:", error);
    }
  };

  const handleMoveToFolder = async (folderId) => {
    if (!selectedConversation) return;
    try {
      await Conversation.update(selectedConversation.id, { folder_id: folderId });
      await loadData(false);
      setIsMoveToFolderModalOpen(false);
    } catch (error) {
      console.error("Error moving conversation to folder:", error);
    }
  };

  // ===== FILTERING AND SEARCH =====

  /**
   * Filters conversations based on search query and selected folder
   * Memoized for performance optimization
   */
  const filteredConversations = useCallback(() => {
    return conversations.filter(conv => {
      // Search filter
      const matchesSearch = !searchQuery || 
        conv.title?.toLowerCase().includes(searchQuery.toLowerCase()) ||
        conv.last_message_text?.toLowerCase().includes(searchQuery.toLowerCase());
      
      // Folder filter
      const matchesFolder = !selectedFolder || conv.folder_id === selectedFolder.id;
      
      return matchesSearch && matchesFolder;
    });
  }, [conversations, searchQuery, selectedFolder]);

  // ===== RENDER =====

  return (
    <>
      <div className="flex h-screen bg-gray-50 overflow-hidden">
        {/* 
          LEFT SIDEBAR - CONVERSATIONS AND FOLDERS
          This section contains the conversation list, search, and folder filters
        */}
        <div className="w-96 bg-white border-r border-gray-200 flex flex-col">
          {/* Header with search */}
          <div className="p-4 border-b border-gray-100">
            <div className="flex items-center justify-between mb-4">
              <h1 className="text-xl font-semibold text-gray-900">Messages</h1>
              <button 
                className="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm flex items-center"
                onClick={() => setIsNewConvoModalOpen(true)}
              >
                + New
              </button>
            </div>
            
            {/* Search input */}
            <div className="relative">
              <input
                placeholder="Search conversations..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-md focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm"
              />
            </div>
          </div>

          {/* Folder filter tabs */}
          {folders.length > 0 && (
            <div className="px-4 py-2 border-b border-gray-100">
              <div className="flex gap-2 overflow-x-auto">
                <button
                  className={`px-3 py-1 text-sm rounded shrink-0 ${
                    selectedFolder === null 
                      ? 'bg-blue-600 text-white' 
                      : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                  }`}
                  onClick={() => setSelectedFolder(null)}
                >
                  All
                </button>
                {folders.map((folder) => (
                  <button
                    key={folder.id}
                    className={`px-3 py-1 text-sm rounded shrink-0 flex items-center ${
                      selectedFolder?.id === folder.id 
                        ? 'bg-blue-600 text-white' 
                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                    }`}
                    onClick={() => setSelectedFolder(folder)}
                  >
                    📁 {folder.name}
                  </button>
                ))}
              </div>
            </div>
          )}

          {/* Conversation list */}
          <div className="flex-1 overflow-y-auto">
            <ConversationList
              conversations={filteredConversations()}
              contacts={contacts}
              selectedConversation={selectedConversation}
              onSelectConversation={setSelectedConversation}
              isLoading={isLoading}
            />
          </div>
        </div>

        {/* 
          MAIN CHAT AREA
          This section contains the chat header, messages, and input area
        */}
        <div className="flex-1 flex flex-col relative">
          {selectedConversation ? (
            <>
              {/* Chat header with user info and action buttons */}
              <ChatHeader
                conversation={selectedConversation}
                contacts={contacts}
                onProfileClick={handleProfileClick}
                onStartVoiceCall={() => handleStartCall('voice')}
                onStartVideoCall={() => handleStartCall('video')}
                onMoveToFolder={() => setIsMoveToFolderModalOpen(true)}
              />
              
              {/* Messages area */}
              <div className="flex-1 p-6 bg-gray-50 overflow-y-auto">
                <div className="space-y-4">
                  {messages.map((message, index) => (
                    <MessageBubble
                      key={message.id}
                      message={message}
                      contacts={contacts}
                      isOwnMessage={message.sender_id === CURRENT_USER_ID}
                      onReaction={handleReaction}
                      onDelete={handleDeleteMessage}
                      showTimestamp={
                        index === 0 || 
                        new Date(message.created_date).getTime() - 
                        new Date(messages[index - 1].created_date).getTime() > 300000 // 5 minutes
                      }
                    />
                  ))}
                  
                  {/* Typing indicator */}
                  {isTyping && (
                    <TypingIndicator 
                      contacts={contacts} 
                      conversation={selectedConversation} 
                    />
                  )}
                  
                  {/* Scroll anchor */}
                  <div ref={messagesEndRef} />
                </div>
              </div>
              
              {/* Message input area */}
              <MessageInput
                onSendMessage={handleSendMessage}
                onFileUpload={handleFileUpload}
                onTyping={handleTyping}
                isSending={isSending}
              />
            </>
          ) : (
            /* Empty state when no conversation is selected */
            <EmptyState onNewConversation={() => setIsNewConvoModalOpen(true)} />
          )}
        </div>

        {/* 
          PROFILE SIDEBAR
          Sliding sidebar that shows contact details when a profile is clicked
        */}
        {isProfileOpen && profileContact && (
          <ProfileSidebar
            contact={profileContact}
            onClose={() => setIsProfileOpen(false)}
            onStartConversation={(contactId) => {
              // TODO: Implement starting new conversation with contact
              console.log("Starting conversation with:", contactId);
              setIsProfileOpen(false);
            }}
          />
        )}
      </div>

      {/* MODALS */}
      <NewConversationModal
        isOpen={isNewConvoModalOpen}
        onClose={() => setIsNewConvoModalOpen(false)}
        contacts={contacts.filter(c => c.id !== CURRENT_USER_ID)}
        onCreateConversation={handleCreateConversation}
      />
      <MoveToFolderModal
        isOpen={isMoveToFolderModalOpen}
        onClose={() => setIsMoveToFolderModalOpen(false)}
        folders={folders}
        onMoveToFolder={handleMoveToFolder}
      />
    </>
  );
}
