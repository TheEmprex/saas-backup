import React from 'react';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';

/**
 * TYPING INDICATOR COMPONENT
 * 
 * Shows when someone is typing in the conversation:
 * - Animated dots to indicate typing activity
 * - User avatar and name
 * - Smooth animations for show/hide
 * 
 * CUSTOMIZATION:
 * - Adjust animation timing in the CSS classes
 * - Modify the typing text or add internationalization
 * - Change dot colors and styling
 */

export default function TypingIndicator({ contacts, conversation }) {
  // ===== HELPER FUNCTIONS =====
  
  /**
   * Get contact information by ID
   */
  const getContactById = (id) => contacts.find(c => c.id === id);
  
  /**
   * Get the user who is currently typing
   * In a real app, this would come from real-time data
   */
  const getTypingUser = () => {
    // Find the first participant who isn't the current user
    const otherParticipant = conversation.participants.find(p => p !== "current-user");
    return getContactById(otherParticipant);
  };

  const typingUser = getTypingUser();

  // Don't render if no typing user found
  if (!typingUser) return null;

  // ===== RENDER =====

  return (
    <div className="flex items-center gap-3 animate-fade-in">
      {/* User avatar */}
      <Avatar className="w-8 h-8 shrink-0">
        <AvatarImage src={typingUser.avatar_url} />
        <AvatarFallback className="bg-gray-200 text-gray-600 text-xs font-medium">
          {typingUser.name.charAt(0).toUpperCase()}
        </AvatarFallback>
      </Avatar>
      
      {/* Typing indicator bubble */}
      <div className="px-4 py-3 rounded-2xl bg-white border border-gray-200 shadow-sm">
        <div className="flex items-center gap-1">
          {/* Animated typing dots */}
          <span className="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: '0ms' }}></span>
          <span className="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: '150ms' }}></span>
          <span className="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style={{ animationDelay: '300ms' }}></span>
        </div>
      </div>
      
      {/* Typing text */}
      <div className="text-xs text-gray-500">
        <span className="font-medium">{typingUser.name}</span> is typing...
      </div>
    </div>
  );
}