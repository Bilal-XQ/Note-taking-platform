import React from 'react';
import { motion } from 'framer-motion';
import { useInView } from 'react-intersection-observer';
import Badge from '../UI/Badge';

const FeatureCard = ({ 
  feature, 
  index, 
  isLarge = false,
  isWide = false 
}) => {
  const [ref, inView] = useInView({
    threshold: 0.1,
    triggerOnce: true
  });

  const cardVariants = {
    hidden: { 
      opacity: 0, 
      y: 50, 
      scale: 0.95 
    },
    visible: { 
      opacity: 1, 
      y: 0, 
      scale: 1,
      transition: {
        duration: 0.6,
        delay: index * 0.1,
        ease: "easeOut"
      }
    }
  };

  const iconVariants = {
    hidden: { scale: 0, rotate: -180 },
    visible: { 
      scale: 1, 
      rotate: 0,
      transition: {
        duration: 0.8,
        delay: index * 0.1 + 0.3,
        type: "spring",
        stiffness: 200
      }
    }
  };

  const contentVariants = {
    hidden: { opacity: 0, y: 20 },
    visible: { 
      opacity: 1, 
      y: 0,
      transition: {
        duration: 0.6,
        delay: index * 0.1 + 0.4
      }
    }
  };

  const getCardClasses = () => {
    let classes = "group relative overflow-hidden ";
    
    if (isLarge && isWide) {
      classes += "col-span-2 row-span-2 ";
    } else if (isLarge) {
      classes += "row-span-2 ";
    } else if (isWide) {
      classes += "col-span-2 ";
    }
    
    return classes;
  };

  return (
    <motion.div
      ref={ref}
      className={getCardClasses()}
      variants={cardVariants}
      initial="hidden"
      animate={inView ? "visible" : "hidden"}
      whileHover={{ 
        scale: 1.02, 
        y: -8,
        transition: { duration: 0.3 }
      }}
    >
      <div className={`h-full glass-strong rounded-3xl p-6 lg:p-8 shadow-xl hover:shadow-2xl transition-all duration-500 ${
        feature.highlight ? 'ring-2 ring-primary-500/50' : ''
      }`}>
        {/* Background Gradient */}
        <div className={`absolute inset-0 bg-gradient-to-br ${feature.gradient} opacity-5 rounded-3xl transition-opacity duration-500 group-hover:opacity-10`} />
        
        {/* Content */}
        <div className="relative z-10 h-full flex flex-col">
          {/* Header */}
          <div className="flex items-start justify-between mb-6">
            <motion.div
              className={`w-16 h-16 bg-gradient-to-br ${feature.gradient} rounded-2xl flex items-center justify-center shadow-lg`}
              variants={iconVariants}
            >
              <feature.icon className="w-8 h-8 text-white" />
            </motion.div>
            
            {feature.badge && (
              <Badge 
                variant={feature.badge.variant} 
                size="sm"
                className="opacity-90"
              >
                {feature.badge.text}
              </Badge>
            )}
          </div>

          {/* Content */}
          <motion.div 
            className="flex-grow"
            variants={contentVariants}
          >
            <h3 className="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mb-4">
              {feature.title}
            </h3>
            
            <p className="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
              {feature.description}
            </p>

            {/* Additional Content for Large Cards */}
            {(isLarge || isWide) && feature.extraContent && (
              <div className="space-y-4">
                {feature.extraContent.map((item, idx) => (
                  <motion.div
                    key={idx}
                    className="flex items-center gap-3 p-3 rounded-xl bg-gray-50/50 dark:bg-white/5"
                    initial={{ opacity: 0, x: -20 }}
                    animate={inView ? { opacity: 1, x: 0 } : { opacity: 0, x: -20 }}
                    transition={{ delay: index * 0.1 + 0.6 + idx * 0.1 }}
                  >
                    <div className={`w-8 h-8 bg-gradient-to-br ${item.color} rounded-lg flex items-center justify-center`}>
                      <item.icon className="w-4 h-4 text-white" />
                    </div>
                    <div>
                      <div className="font-semibold text-gray-900 dark:text-white text-sm">
                        {item.title}
                      </div>
                      <div className="text-xs text-gray-600 dark:text-gray-400">
                        {item.description}
                      </div>
                    </div>
                  </motion.div>
                ))}
              </div>
            )}
          </motion.div>

          {/* Interactive Elements for Large Cards */}
          {isLarge && feature.interactive && (
            <motion.div
              className="mt-6 p-4 rounded-2xl bg-gradient-to-r from-gray-50/50 to-gray-100/50 dark:from-white/5 dark:to-white/10"
              initial={{ opacity: 0, scale: 0.95 }}
              animate={inView ? { opacity: 1, scale: 1 } : { opacity: 0, scale: 0.95 }}
              transition={{ delay: index * 0.1 + 0.8 }}
            >
              <div className="flex items-center justify-between mb-3">
                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                  {feature.interactive.label}
                </span>
                <span className="text-sm font-bold text-primary-600 dark:text-primary-400">
                  {feature.interactive.value}
                </span>
              </div>
              <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <motion.div
                  className={`h-2 rounded-full bg-gradient-to-r ${feature.gradient}`}
                  initial={{ width: 0 }}
                  animate={inView ? { width: feature.interactive.progress } : { width: 0 }}
                  transition={{ duration: 1.5, delay: index * 0.1 + 1 }}
                />
              </div>
            </motion.div>
          )}

          {/* Hover Effect Overlay */}
          <div className="absolute inset-0 bg-gradient-to-br from-white/0 to-white/5 dark:from-white/0 dark:to-white/5 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
        </div>
      </div>
    </motion.div>
  );
};

export default FeatureCard;
