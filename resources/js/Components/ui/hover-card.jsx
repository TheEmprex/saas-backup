import React from 'react';

// Simple stub components to satisfy imports
export const HoverCard = ({ children }) => children;
export const HoverCardTrigger = ({ children }) => children;
export const HoverCardContent = ({ children, className = "", ...props }) => (
  <div className={className} {...props}>{children}</div>
);
