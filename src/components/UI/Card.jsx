import React from 'react';
import { motion } from 'framer-motion';

const Card = ({ 
  children, 
  variant = 'default', 
  className = '', 
  hover = true,
  gradient = false,
  ...props 
}) => {
  const variants = {
    default: "bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg",
    glass: "glass",
    neumorphic: "neumorphic",
    gradient: "bg-gradient-to-br from-primary-500/10 to-electric-500/10 border border-primary-200/50 dark:border-primary-700/50"
  };

  const hoverClass = hover ? "floating-card" : "";
  const gradientOverlay = gradient ? "relative overflow-hidden" : "";

  return (
    <motion.div
      className={`rounded-3xl p-6 ${variants[variant]} ${hoverClass} ${gradientOverlay} ${className}`}
      initial={{ opacity: 0, y: 20 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
      transition={{ duration: 0.6, ease: "easeOut" }}
      {...props}
    >
      {gradient && (
        <div className="absolute inset-0 bg-gradient-to-br from-primary-500/5 to-electric-500/5 rounded-3xl" />
      )}
      <div className="relative z-10">
        {children}
      </div>
    </motion.div>
  );
};

export default Card;
