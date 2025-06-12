import React from 'react';
import { motion } from 'framer-motion';
import { useInView } from 'react-intersection-observer';
import { 
  Layers, 
  Edit3, 
  Brain, 
  Users, 
  Zap, 
  BarChart3 
} from 'lucide-react';

const FeatureGrid = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.1
  });

  const features = [
    {
      icon: Layers,
      title: "Smart Organization",
      description: "Automatically organize notes by course modules with AI-powered categorization and tagging.",
      highlight: "Popular"
    },
    {
      icon: Edit3,
      title: "Rich Text Editor",
      description: "Professional editor with markdown support, real-time collaboration, and seamless syncing.",
      highlight: "Premium"
    },
    {
      icon: Brain,
      title: "AI Study Assistant",
      description: "Get AI-generated summaries, flashcards, and personalized quizzes to enhance learning.",
      highlight: "New"
    },
    {
      icon: Users,
      title: "Team Collaboration",
      description: "Share notes with classmates, create study groups, and collaborate in real-time.",
      highlight: null
    },
    {
      icon: Zap,
      title: "Quick Capture",
      description: "Instantly capture ideas, voice notes, and images with our mobile app integration.",
      highlight: null
    },
    {
      icon: BarChart3,
      title: "Study Analytics",
      description: "Track your study habits, monitor progress, and get insights to improve performance.",
      highlight: "Coming Soon"
    }
  ];

  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.1
      }
    }
  };

  const cardVariants = {
    hidden: { opacity: 0, y: 20 },
    visible: {
      opacity: 1,
      y: 0,
      transition: {
        duration: 0.6,
        ease: "easeOut"
      }
    }
  };

  return (
    <section id="features" className="section">
      <div className="container">
        {/* Section Header */}
        <div className="section-header">
          <div className="inline-flex items-center gap-2 bg-accent-100 text-accent-700 px-3 py-1 rounded-full text-sm font-medium mb-6">
            <Zap className="w-4 h-4" />
            Features
          </div>
          <h2 className="section-title">
            Everything you need to excel
          </h2>
          <p className="section-subtitle">
            Powerful tools designed specifically for modern students to organize, learn, and succeed.
          </p>
        </div>

        {/* Features Grid */}
        <motion.div
          ref={ref}
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
          variants={containerVariants}
          initial="hidden"
          animate={inView ? "visible" : "hidden"}
        >
          {features.map((feature, index) => (
            <motion.div
              key={index}
              variants={cardVariants}
              className="card card-interactive h-full"
            >
              {/* Icon */}
              <div className="mb-6">
                <div className="w-12 h-12 bg-gradient-to-br from-accent-500 to-accent-600 rounded-xl flex items-center justify-center mb-4">
                  <feature.icon className="w-6 h-6 text-white" />
                </div>
                {feature.highlight && (
                  <span className={`inline-flex px-2 py-1 rounded-full text-xs font-medium ${
                    feature.highlight === 'Popular' ? 'bg-success-100 text-success-700' :
                    feature.highlight === 'Premium' ? 'bg-accent-100 text-accent-700' :
                    feature.highlight === 'New' ? 'bg-purple-100 text-purple-700' :
                    'bg-orange-100 text-orange-700'
                  }`}>
                    {feature.highlight}
                  </span>
                )}
              </div>

              {/* Content */}
              <div>
                <h3 className="text-xl font-semibold text-primary-900 dark:text-white mb-3">
                  {feature.title}
                </h3>
                <p className="text-primary-600 dark:text-primary-300 leading-relaxed">
                  {feature.description}
                </p>
              </div>
            </motion.div>
          ))}
        </motion.div>
      </div>
    </section>
  );
};

export default FeatureGrid;
