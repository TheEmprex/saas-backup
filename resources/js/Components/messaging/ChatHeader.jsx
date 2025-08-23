import React from 'react';
import { MoreVertical, Phone, Video } from 'lucide-react';

const ChatHeader = ({ conversation }) => {
  if (!conversation) {
    return (
      <div className="p-4 border-b border-gray-200 bg-white">
        <div className="text-center text-gray-500">
          Select a conversation to start messaging
        </div>
      </div>
    );
  }

  return (
    <div className="p-4 border-b border-gray-200 bg-white">
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-3">
          <div className="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
            {conversation.name.charAt(0).toUpperCase()}
          </div>
          <div>
            <h3 className="text-lg font-semibold text-gray-900">
              {conversation.name}
            </h3>
            <p className="text-sm text-gray-500">
              Online
            </p>
          </div>
        </div>
        
        <div className="flex items-center space-x-2">
          <button className="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <Phone size={18} />
          </button>
          <button className="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <Video size={18} />
          </button>
          <button className="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <MoreVertical size={18} />
          </button>
        </div>
      </div>
    </div>
  );
};

export default ChatHeader;
