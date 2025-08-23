
import React from "react";
import { MessageSquare, Users } from "lucide-react";

export default function EmptyState({ onNewConversation }) {
  return (
    <div className="flex-1 flex items-center justify-center bg-gray-50">
      <div className="text-center max-w-md px-6">
        <div className="w-20 h-20 mx-auto mb-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center">
          <MessageSquare className="w-10 h-10 text-white" />
        </div>
        
        <h2 className="text-2xl font-semibold text-gray-900 mb-3">
          Welcome to Messages
        </h2>
        
        <p className="text-gray-600 mb-6 leading-relaxed">
          Select a conversation from the sidebar to start messaging, or create a new conversation to connect with agencies and chatters.
        </p>
        
        <div className="space-y-3">
          <button 
            className="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center justify-center"
            onClick={onNewConversation}
          >
            <Users className="w-4 h-4 mr-2" />
            Start New Conversation
          </button>
        </div>
        
        <div className="mt-8 p-4 bg-blue-50 rounded-lg">
          <h3 className="font-medium text-blue-900 mb-2">Quick Tips</h3>
          <ul className="text-sm text-blue-700 space-y-1 text-left">
            <li>• Use folders to organize your conversations</li>
            <li>• Click the phone or video icons to start calls</li>
            <li>• Drag and drop files to share them instantly</li>
          </ul>
        </div>
      </div>
    </div>
  );
}
