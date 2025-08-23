import React from 'react';

export const Card = ({ children, className = "", ...props }) => (
  <div className={`bg-white border border-gray-200 rounded-lg shadow-sm ${className}`} {...props}>
    {children}
  </div>
);

export const CardHeader = ({ children, className = "", ...props }) => (
  <div className={`p-6 pb-0 ${className}`} {...props}>{children}</div>
);

export const CardTitle = ({ children, className = "", ...props }) => (
  <h3 className={`text-lg font-semibold ${className}`} {...props}>{children}</h3>
);

export const CardContent = ({ children, className = "", ...props }) => (
  <div className={`p-6 ${className}`} {...props}>{children}</div>
);
