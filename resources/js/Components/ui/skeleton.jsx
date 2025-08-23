import React from 'react';

export const Skeleton = ({ className = "", ...props }) => (
  <div className={`bg-gray-200 rounded animate-pulse ${className}`} {...props} />
);
