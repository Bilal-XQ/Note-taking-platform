import React from 'react';
import { motion } from 'framer-motion';
import { BookOpen, StickyNote, Brain, Lightbulb, PenTool, TrendingUp } from 'lucide-react';
import { useMousePosition } from '../../hooks/useAnimations';

const FloatingCards = () => {
  const mousePosition = useMousePosition();

  const cards = [
    {
      id: 1,
      icon: BookOpen,
      content: ['Mathematics Notes', 'Chapter 5: Calculus', 'Integration Methods'],
      position: { top: '10%', left: '10%' },
      delay: 0,
      color: 'from-blue-500 to-indigo-600'
    },
    {
      id: 2,
      icon: StickyNote,
      content: ['Quick Reminder', '• Submit assignment', '• Review chapter 3'],
      position: { top: '50%', left: '5%' },
      delay: 2,
      color: 'from-yellow-500 to-orange-600'
    },
    {
      id: 3,
      icon: Brain,
      content: ['AI Summary', 'Key concepts identified', 'Study time: 2.5hrs'],
      position: { top: '20%', right: '15%' },
      delay: 4,
      color: 'from-purple-500 to-pink-600'
    },
    {
      id: 4,
      icon: TrendingUp,
      content: ['Progress', '85% completion', 'Great job!'],
      position: { bottom: '30%', right: '10%' },
      delay: 1,
      color: 'from-green-500 to-emerald-600'
    }
  ];

  const floatingElements = [
    { icon: Lightbulb, position: { top: '15%', right: '25%' }, delay: 0 },
    { icon: PenTool, position: { bottom: '40%', left: '15%' }, delay: 3 },
    { icon: TrendingUp, position: { top: '60%', right: '20%' }, delay: 6 }
  ];

  return (
    <div className="absolute inset-0 pointer-events-none">
      {/* Main floating cards */}
      {cards.map((card) => (
        <motion.div
          key={card.id}
          className="absolute w-48 md:w-56 lg:w-64"
          style={{
            ...card.position,
            transform: `translate(${(mousePosition.x - window.innerWidth / 2) * 0.02}px, ${(mousePosition.y - window.innerHeight / 2) * 0.02}px)`
          }}
          initial={{ opacity: 0, scale: 0.8, y: 50 }}
          animate={{ 
            opacity: 1, 
            scale: 1, 
            y: 0,
            rotateY: (mousePosition.x - window.innerWidth / 2) * 0.01,
            rotateX: (mousePosition.y - window.innerHeight / 2) * -0.01
          }}
          transition={{ 
            duration: 0.8, 
            delay: card.delay,
            type: "spring",
            stiffness: 100
          }}
          whileHover={{ 
            scale: 1.05, 
            y: -10,
            transition: { duration: 0.3 }
          }}
        >
          <div className="glass-strong rounded-3xl p-6 shadow-2xl transform-gpu perspective-1000">
            <div className={`w-12 h-12 bg-gradient-to-br ${card.color} rounded-2xl flex items-center justify-center mb-4 shadow-lg`}>
              <card.icon className="w-6 h-6 text-white" />
            </div>
            
            <div className="space-y-2">
              {card.content.map((line, index) => (
                <div
                  key={index}
                  className={`${index === 0 ? 'font-semibold text-gray-900 dark:text-white' : 'text-sm text-gray-600 dark:text-gray-400'}`}
                >
                  {line}
                </div>
              ))}
            </div>
            
            {/* Animated progress bar for some cards */}
            {card.id === 4 && (
              <div className="mt-4">
                <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                  <motion.div
                    className="bg-gradient-to-r from-green-500 to-emerald-600 h-2 rounded-full"
                    initial={{ width: 0 }}
                    animate={{ width: '85%' }}
                    transition={{ duration: 2, delay: card.delay + 1 }}
                  />
                </div>
              </div>
            )}
            
            {/* Pulse indicator for active cards */}
            {card.id === 3 && (
              <motion.div
                className="absolute -top-2 -right-2 w-4 h-4 bg-green-500 rounded-full"
                animate={{ scale: [1, 1.2, 1], opacity: [1, 0.7, 1] }}
                transition={{ duration: 2, repeat: Infinity }}
              />
            )}
          </div>
        </motion.div>
      ))}

      {/* Smaller floating elements */}
      {floatingElements.map((element, index) => (
        <motion.div
          key={index}
          className="absolute w-16 h-16 glass rounded-full flex items-center justify-center"
          style={element.position}
          initial={{ opacity: 0, scale: 0 }}
          animate={{ 
            opacity: 0.7, 
            scale: 1,
            y: [0, -10, 0],
            rotate: [0, 180, 360]
          }}
          transition={{
            duration: 0.8,
            delay: element.delay,
            y: { duration: 4, repeat: Infinity, ease: "easeInOut" },
            rotate: { duration: 10, repeat: Infinity, ease: "linear" }
          }}
        >
          <element.icon className="w-6 h-6 text-primary-600 dark:text-primary-400" />
        </motion.div>
      ))}

      {/* Background orbs */}
      <motion.div
        className="floating-orb w-96 h-96 bg-primary-500"
        style={{ top: '20%', left: '10%' }}
        animate={{
          x: [0, 100, 0],
          y: [0, -50, 0],
          scale: [1, 1.2, 1]
        }}
        transition={{
          duration: 20,
          repeat: Infinity,
          ease: "easeInOut"
        }}
      />
      
      <motion.div
        className="floating-orb w-64 h-64 bg-electric-500"
        style={{ bottom: '20%', right: '15%' }}
        animate={{
          x: [0, -80, 0],
          y: [0, 60, 0],
          scale: [1, 0.8, 1]
        }}
        transition={{
          duration: 15,
          repeat: Infinity,
          ease: "easeInOut",
          delay: 5
        }}
      />
      
      <motion.div
        className="floating-orb w-48 h-48 bg-neon-500"
        style={{ top: '60%', left: '70%' }}
        animate={{
          x: [0, 60, 0],
          y: [0, -80, 0],
          scale: [1, 1.1, 1]
        }}
        transition={{
          duration: 18,
          repeat: Infinity,
          ease: "easeInOut",
          delay: 10
        }}
      />
    </div>
  );
};

export default FloatingCards;
