import React from 'react';

export const Select = ({ children, value, onValueChange }) => (
  <div className="relative">{children}</div>
);

export const SelectTrigger = ({ children, className = "", ...props }) => (
  <button className={`w-full px-3 py-2 text-left bg-white border border-gray-300 rounded-md ${className}`} {...props}>
    {children}
  </button>
);

export const SelectValue = ({ placeholder }) => (
  <span className="text-gray-500">{placeholder}</span>
);

export const SelectContent = ({ children, className = "", ...props }) => (
  <div className={`absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg ${className}`} {...props}>
    {children}
  </div>
);

export const SelectItem = ({ children, value, className = "", ...props }) => (
  <div className={`px-3 py-2 hover:bg-gray-100 cursor-pointer ${className}`} {...props}>
    {children}
  </div>
);
