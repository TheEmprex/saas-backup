import React from 'react';

export const Dialog = ({ children, open, onOpenChange }) => (
  open ? <div className="fixed inset-0 z-50 flex items-center justify-center">{children}</div> : null
);

export const DialogTrigger = ({ children }) => children;

export const DialogContent = ({ children, className = "", ...props }) => (
  <div className={`bg-white border border-gray-200 rounded-lg shadow-lg p-6 max-w-md w-full ${className}`} {...props}>
    {children}
  </div>
);

export const DialogHeader = ({ children, className = "", ...props }) => (
  <div className={`mb-4 ${className}`} {...props}>{children}</div>
);

export const DialogTitle = ({ children, className = "", ...props }) => (
  <h2 className={`text-lg font-semibold ${className}`} {...props}>{children}</h2>
);

export const DialogFooter = ({ children, className = "", ...props }) => (
  <div className={`mt-4 flex justify-end space-x-2 ${className}`} {...props}>{children}</div>
);

export const DialogClose = ({ children, onClick, className = "", ...props }) => (
  <button className={`px-4 py-2 text-sm font-medium ${className}`} onClick={onClick} {...props}>
    {children}
  </button>
);
