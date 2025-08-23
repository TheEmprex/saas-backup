import React from 'react';

export const Sidebar = ({ children, className = "", ...props }) => (
  <aside className={`fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 ${className}`} {...props}>
    {children}
  </aside>
);

export const SidebarContent = ({ children, className = "", ...props }) => (
  <div className={`h-full overflow-y-auto ${className}`} {...props}>{children}</div>
);
