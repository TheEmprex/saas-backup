import React from 'react';
import { Send } from 'lucide-react';

const MessageInput = ({ 
  messageText, 
  onMessageChange, 
  onSendMessage 
}) => {
  const handleKeyPress = (e) => {
    if (e.key === 'Enter') {
      onSendMessage();
    }
  };

  return (
    <div className="p-3 border-t border-gray-200">
      <div className="flex items-center">
        <input
          type="text"
          value={messageText}
          onChange={onMessageChange}
          onKeyPress={handleKeyPress}
          placeholder="Type a message..."
          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
        <button
          onClick={onSendMessage}
          className="ml-3 p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
        >
          <Send size={20} />
        </button>
      </div>
    </div>
  );
};

export default MessageInput;
