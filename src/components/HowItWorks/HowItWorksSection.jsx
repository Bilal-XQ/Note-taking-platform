import React from 'react';
import { motion } from 'framer-motion';
import { useInView } from 'react-intersection-observer';
import { UserPlus, Plus, PenTool, Trophy } from 'lucide-react';

const HowItWorksSection = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.1
  });

  const steps = [
    {
      number: "01",
      icon: UserPlus,
      title: "Create Account",
      description: "Sign up in seconds and set up your personalized study dashboard."
    },
    {
      number: "02", 
      icon: Plus,
      title: "Add Modules",
      description: "Import your courses or manually add modules to organize your studies."
    },
    {
      number: "03",
      icon: PenTool,
      title: "Take Notes",
      description: "Use our powerful editor to create, organize, and enhance your notes."
    },
    {
      number: "04",
      icon: Trophy,
      title: "Excel in Studies",
      description: "Review with AI summaries, practice with quizzes, and ace your exams."
    }
  ];

  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.2
      }
    }
  };

  const stepVariants = {
    hidden: { opacity: 0, y: 30 },
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
    <section id="how-it-works" className="section bg-primary-50 dark:bg-primary-900">
      <div className="container">
        {/* Section Header */}
        <div className="section-header">
          <div className="inline-flex items-center gap-2 bg-success-100 text-success-700 px-3 py-1 rounded-full text-sm font-medium mb-6">
            <Trophy className="w-4 h-4" />
            How it works
          </div>
          <h2 className="section-title">
            Start studying smarter in 4 steps
          </h2>
          <p className="section-subtitle">
            Get up and running with StudyNotes in just a few minutes.
          </p>
        </div>

        {/* Steps */}
        <motion.div
          ref={ref}
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8"
          variants={containerVariants}
          initial="hidden"
          animate={inView ? "visible" : "hidden"}
        >
          {steps.map((step, index) => (
            <motion.div
              key={index}
              variants={stepVariants}
              className="text-center"
            >
              {/* Step Number */}
              <div className="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-accent-500 to-accent-600 rounded-2xl text-white font-bold text-lg mb-6">
                {step.number}
              </div>

              {/* Icon */}
              <div className="w-12 h-12 bg-white dark:bg-primary-800 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-primary-200 dark:border-primary-700">
                <step.icon className="w-6 h-6 text-accent-600" />
              </div>

              {/* Content */}
              <h3 className="text-lg font-semibold text-primary-900 dark:text-white mb-3">
                {step.title}
              </h3>
              
              <p className="text-primary-600 dark:text-primary-300 leading-relaxed">
                {step.description}
              </p>
            </motion.div>
          ))}
        </motion.div>
      </div>
    </section>
  );
};

export default HowItWorksSection;