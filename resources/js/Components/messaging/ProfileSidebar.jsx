import React from 'react';
import { motion } from 'framer-motion';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { 
  X, 
  Mail, 
  Phone, 
  Video, 
  Building2, 
  MessageSquare, 
  Circle,
  Calendar,
  Globe,
  MapPin,
  Star
} from 'lucide-react';
import { format } from "date-fns";

/**
 * PROFILE SIDEBAR COMPONENT
 * 
 * Displays detailed contact information in a sliding sidebar:
 * - Contact details and avatar
 * - Online status and last seen
 * - Company and role information
 * - Quick action buttons
 * - Contact statistics
 * 
 * CUSTOMIZATION:
 * - Modify getRoleColor for custom role styling
 * - Add additional contact fields in the details section
 * - Customize action buttons and their functionality
 */

export default function ProfileSidebar({ contact, onClose, onStartConversation }) {
  if (!contact) return null;

  // ===== HELPER FUNCTIONS =====

  /**
   * Get role-specific styling
   */
  const getRoleColor = (role) => {
    switch (role) {
      case 'chatter': 
        return {
          badge: 'bg-purple-100 text-purple-800 border-purple-200',
          gradient: 'from-purple-500 to-pink-600'
        };
      case 'agency': 
        return {
          badge: 'bg-blue-100 text-blue-800 border-blue-200',
          gradient: 'from-blue-500 to-indigo-600'
        };
      case 'admin': 
        return {
          badge: 'bg-green-100 text-green-800 border-green-200',
          gradient: 'from-green-500 to-emerald-600'
        };
      default: 
        return {
          badge: 'bg-gray-100 text-gray-800 border-gray-200',
          gradient: 'from-gray-500 to-slate-600'
        };
    }
  };

  /**
   * Get formatted status text
   */
  const getStatusText = (contact) => {
    if (contact.is_online) return 'Online now';
    if (contact.last_seen) {
      return `Last seen ${format(new Date(contact.last_seen), 'MMM d, HH:mm')}`;
    }
    return 'Offline';
  };

  /**
   * Get initials for avatar fallback
   */
  const getInitials = (name) => {
    return name
      .split(' ')
      .map(word => word.charAt(0))
      .join('')
      .toUpperCase()
      .slice(0, 2);
  };

  // ===== EVENT HANDLERS =====

  const handleStartConversation = () => {
    if (onStartConversation) {
      onStartConversation(contact.id);
    }
  };

  const handleVoiceCall = () => {
    // TODO: Implement voice call functionality
    console.log('Starting voice call with:', contact.name);
  };

  const handleVideoCall = () => {
    // TODO: Implement video call functionality
    console.log('Starting video call with:', contact.name);
  };

  const handleEmailContact = () => {
    window.open(`mailto:${contact.email}`, '_blank');
  };

  // ===== RENDER =====

  const roleStyle = getRoleColor(contact.role);

  return (
    <motion.div
      initial={{ x: '100%' }}
      animate={{ x: 0 }}
      exit={{ x: '100%' }}
      transition={{ 
        type: 'spring', 
        stiffness: 300, 
        damping: 30,
        mass: 1
      }}
      className="absolute top-0 right-0 h-full w-96 bg-white border-l border-gray-200 shadow-2xl z-20 flex flex-col overflow-hidden"
    >
      {/* Header */}
      <header className="p-6 border-b border-gray-200 flex items-center justify-between bg-gray-50">
        <h2 className="text-lg font-semibold text-gray-900">Contact Profile</h2>
        <Button 
          variant="ghost" 
          size="icon" 
          onClick={onClose}
          className="hover:bg-gray-200"
        >
          <X className="w-5 h-5" />
        </Button>
      </header>

      {/* Profile content */}
      <div className="flex-1 overflow-y-auto">
        {/* Contact header */}
        <div className="p-6 bg-gradient-to-br from-gray-50 to-white">
          <div className="flex flex-col items-center text-center">
            <div className="relative mb-4">
              <Avatar className="w-24 h-24 border-4 border-white shadow-lg">
                <AvatarImage src={contact.avatar_url} />
                <AvatarFallback className={`text-2xl bg-gradient-to-r ${roleStyle.gradient} text-white font-bold`}>
                  {getInitials(contact.name)}
                </AvatarFallback>
              </Avatar>
              
              {/* Online status indicator */}
              <div className={`absolute bottom-2 right-2 w-6 h-6 border-4 border-white rounded-full ${
                contact.is_online ? 'bg-green-500' : 'bg-gray-400'
              }`}></div>
              
              {/* VIP/Premium indicator (if needed) */}
              {contact.role === 'admin' && (
                <div className="absolute -top-1 -right-1 w-6 h-6 bg-yellow-400 border-2 border-white rounded-full flex items-center justify-center">
                  <Star className="w-3 h-3 text-yellow-800" />
                </div>
              )}
            </div>
            
            <h3 className="text-xl font-bold text-gray-900 mb-2">{contact.name}</h3>
            
            <Badge variant="secondary" className={`${roleStyle.badge} border text-sm mb-3`}>
              {contact.role.charAt(0).toUpperCase() + contact.role.slice(1)}
            </Badge>
            
            <div className="flex items-center gap-2 text-sm text-gray-600">
              <Circle className={`w-2 h-2 ${
                contact.is_online ? 'fill-green-500 text-green-500' : 'fill-gray-400 text-gray-400'
              }`} />
              <span>{getStatusText(contact)}</span>
            </div>
          </div>
        </div>

        {/* Contact details */}
        <div className="p-6 space-y-6">
          {/* Basic information */}
          <Card>
            <CardContent className="p-4 space-y-4">
              <h4 className="font-semibold text-gray-900 mb-3">Contact Information</h4>
              
              <div className="flex items-start gap-4">
                <Mail className="w-5 h-5 mt-0.5 text-gray-400" />
                <div className="flex-1 min-w-0">
                  <p className="text-xs text-gray-500 uppercase tracking-wide">Email</p>
                  <button
                    onClick={handleEmailContact}
                    className="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline truncate block w-full text-left"
                  >
                    {contact.email}
                  </button>
                </div>
              </div>
              
              {contact.company && (
                <div className="flex items-start gap-4">
                  <Building2 className="w-5 h-5 mt-0.5 text-gray-400" />
                  <div className="flex-1">
                    <p className="text-xs text-gray-500 uppercase tracking-wide">Company</p>
                    <p className="text-sm font-medium text-gray-900">{contact.company}</p>
                  </div>
                </div>
              )}
              
              {contact.phone && (
                <div className="flex items-start gap-4">
                  <Phone className="w-5 h-5 mt-0.5 text-gray-400" />
                  <div className="flex-1">
                    <p className="text-xs text-gray-500 uppercase tracking-wide">Phone</p>
                    <a 
                      href={`tel:${contact.phone}`}
                      className="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline"
                    >
                      {contact.phone}
                    </a>
                  </div>
                </div>
              )}
              
              <div className="flex items-start gap-4">
                <Calendar className="w-5 h-5 mt-0.5 text-gray-400" />
                <div className="flex-1">
                  <p className="text-xs text-gray-500 uppercase tracking-wide">Member since</p>
                  <p className="text-sm font-medium text-gray-900">
                    {format(new Date(contact.created_date), 'MMMM yyyy')}
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Activity stats (placeholder - you can add real data) */}
          <Card>
            <CardContent className="p-4">
              <h4 className="font-semibold text-gray-900 mb-3">Activity</h4>
              
              <div className="grid grid-cols-2 gap-4">
                <div className="text-center">
                  <div className="text-2xl font-bold text-blue-600">24</div>
                  <div className="text-xs text-gray-500">Messages</div>
                </div>
                <div className="text-center">
                  <div className="text-2xl font-bold text-green-600">5</div>
                  <div className="text-xs text-gray-500">Calls</div>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Additional info section (customizable) */}
          <Card>
            <CardContent className="p-4">
              <h4 className="font-semibold text-gray-900 mb-3">Quick Info</h4>
              
              <div className="space-y-2 text-sm">
                <div className="flex justify-between">
                  <span className="text-gray-500">Timezone</span>
                  <span className="font-medium">UTC-5 (EST)</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-500">Language</span>
                  <span className="font-medium">English</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-500">Response time</span>
                  <span className="font-medium text-green-600">Usually fast</span>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Action buttons footer */}
      <footer className="p-6 border-t border-gray-200 bg-gray-50">
        <div className="space-y-3">
          {/* Primary action */}
          <Button 
            className="w-full bg-blue-600 hover:bg-blue-700" 
            onClick={handleStartConversation}
          >
            <MessageSquare className="w-4 h-4 mr-2" />
            Send Message
          </Button>
          
          {/* Secondary actions */}
          <div className="grid grid-cols-2 gap-3">
            <Button 
              variant="outline" 
              onClick={handleVoiceCall}
              className="flex-1"
            >
              <Phone className="w-4 h-4 mr-2" />
              Call
            </Button>
            <Button 
              variant="outline" 
              onClick={handleVideoCall}
              className="flex-1"
            >
              <Video className="w-4 h-4 mr-2" />
              Video
            </Button>
          </div>
        </div>
      </footer>
    </motion.div>
  );
}