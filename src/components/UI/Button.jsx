import React from 'react';
import { motion } from 'framer-motion';

const Button = ({ 
  children, 
  variant = 'primary', 
  size = 'md', 
  className = '', 
  icon: Icon,
  iconPosition = 'left',
  ...props 
}) => {
  const baseClasses = "inline-flex items-center justify-center font-semibold transition-all duration-300 ease-out focus:outline-none focus:ring-2 disabled:opacity-50 disabled:cursor-not-allowed";
  
  const variants = {
    primary: "bg-gradient-to-r from-primary-600 to-electric-600 text-white shadow-lg shadow-primary-500/25 hover:shadow-xl hover:shadow-primary-500/30 focus:ring-primary-500",
    secondary: "bg-white/10 border border-white/20 text-white backdrop-blur-xl hover:bg-white/20 hover:border-white/30 focus:ring-white/50",
    ghost: "text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:ring-gray-300",
    outline: "border-2 border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white focus:ring-primary-500",
    glass: "glass text-gray-900 dark:text-white hover:bg-white/30 dark:hover:bg-white/10 focus:ring-primary-500"
  };
  
  const sizes = {
    sm: "px-4 py-2 text-sm rounded-xl gap-1.5",
    md: "px-6 py-3 text-base rounded-2xl gap-2",
    lg: "px-8 py-4 text-lg rounded-2xl gap-2.5",
    xl: "px-10 py-5 text-xl rounded-3xl gap-3"
  };

  const hoverEffects = {
    primary: "hover:scale-105 hover:-translate-y-0.5",
    secondary: "hover:scale-105 hover:-translate-y-0.5",
    ghost: "hover:scale-105",
    outline: "hover:scale-105",
    glass: "hover:scale-105 hover:-translate-y-0.5"
  };

  const classes = `${baseClasses} ${variants[variant]} ${sizes[size]} ${hoverEffects[variant]} ${className}`;

  return (
    <motion.button
      className={classes}
      whileHover={{ scale: 1.05, y: -2 }}
      whileTap={{ scale: 0.98 }}
      transition={{ type: "spring", stiffness: 400, damping: 17 }}
      {...props}
    >
      {Icon && iconPosition === 'left' && <Icon className="w-5 h-5" />}
      {children}
      {Icon && iconPosition === 'right' && <Icon className="w-5 h-5" />}
    </motion.button>
  );
};

export default Button;
