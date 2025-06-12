import React from 'react';

const Badge = ({ 
  children, 
  variant = 'default', 
  size = 'md',
  className = '',
  icon: Icon 
}) => {
  const variants = {
    default: "bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200",
    primary: "bg-gradient-to-r from-primary-500 to-electric-500 text-white",
    success: "bg-gradient-to-r from-neon-500 to-neon-400 text-white",
    warning: "bg-gradient-to-r from-yellow-500 to-orange-500 text-white",
    error: "bg-gradient-to-r from-coral-500 to-red-500 text-white",
    glass: "glass text-gray-900 dark:text-white",
    outline: "border-2 border-primary-500 text-primary-600 dark:text-primary-400"
  };

  const sizes = {
    sm: "px-2 py-1 text-xs gap-1",
    md: "px-3 py-1.5 text-sm gap-1.5",
    lg: "px-4 py-2 text-base gap-2"
  };

  return (
    <span className={`inline-flex items-center font-semibold rounded-full ${variants[variant]} ${sizes[size]} ${className}`}>
      {Icon && <Icon className="w-3 h-3" />}
      {children}
    </span>
  );
};

export default Badge;
