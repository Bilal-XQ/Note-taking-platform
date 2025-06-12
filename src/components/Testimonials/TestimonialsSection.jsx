import React from 'react';
import { motion } from 'framer-motion';
import { useInView } from 'react-intersection-observer';
import { Star, Quote } from 'lucide-react';
import Card from '../UI/Card';

const TestimonialsSection = () => {
  const [ref, inView] = useInView({
    triggerOnce: true,
    threshold: 0.1
  });

  const testimonials = [
    {
      id: 1,
      name: "Sarah Chen",
      role: "Computer Science Student",
      university: "MIT",
      avatar: "https://images.unsplash.com/photo-1494790108755-2616b612b830?w=150&h=150&fit=crop&crop=face",
      rating: 5,
      content: "StudyNotes completely transformed how I organize my course materials. The AI summaries are incredibly accurate and save me hours of review time.",
      highlight: "Saved 10+ hours weekly"
    },
    {
      id: 2,
      name: "Marcus Johnson",
      role: "Medical Student",
      university: "Harvard",
      avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face",
      rating: 5,
      content: "The smart organization feature is a game-changer. All my anatomy notes are perfectly categorized, and I can find anything instantly.",
      highlight: "98% exam improvement"
    },
    {
      id: 3,
      name: "Emily Rodriguez",
      role: "Engineering Student",
      university: "Stanford",
      avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face",
      rating: 5,
      content: "I love how seamlessly it syncs across all my devices. I can start notes on my laptop and continue on my phone during commutes.",
      highlight: "Cross-device sync"
    },
    {
      id: 4,
      name: "David Kim",
      role: "Business Student",
      university: "Wharton",
      avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face",
      rating: 5,
      content: "The collaboration features made group projects so much easier. We could all contribute to shared notes in real-time.",
      highlight: "Perfect for teams"
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

  const cardVariants = {
    hidden: { opacity: 0, y: 50 },
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
    <section className="section-padding bg-white dark:bg-gray-900">
      <div className="container-custom">
        {/* Header */}
        <motion.div
          className="text-center mb-20"
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
        >
          <div className="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-white px-4 py-2 rounded-full text-sm font-semibold mb-6">
            <Star className="w-4 h-4" />
            Student Reviews
          </div>
          <h2 className="text-4xl lg:text-5xl font-bold mb-6 gradient-text">
            Loved by Students Worldwide
          </h2>
          <p className="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
            See how StudyNotes is helping students achieve academic excellence
          </p>
        </motion.div>

        {/* Testimonials Grid */}
        <motion.div
          ref={ref}
          className="grid grid-cols-1 md:grid-cols-2 gap-8"
          variants={containerVariants}
          initial="hidden"
          animate={inView ? "visible" : "hidden"}
        >
          {testimonials.map((testimonial, index) => (
            <motion.div
              key={testimonial.id}
              variants={cardVariants}
              className={index % 2 === 1 ? "md:mt-8" : ""}
            >
              <Card className="h-full relative group">
                {/* Quote Icon */}
                <div className="absolute top-6 right-6 opacity-10 group-hover:opacity-20 transition-opacity duration-300">
                  <Quote className="w-12 h-12 text-primary-600" />
                </div>

                {/* Header */}
                <div className="flex items-start gap-4 mb-6">
                  <div className="relative">
                    <img
                      src={testimonial.avatar}
                      alt={testimonial.name}
                      className="w-16 h-16 rounded-2xl object-cover shadow-lg"
                    />
                    <div className="absolute -bottom-2 -right-2 w-6 h-6 bg-gradient-to-br from-green-500 to-emerald-500 rounded-full flex items-center justify-center">
                      <Star className="w-3 h-3 text-white fill-current" />
                    </div>
                  </div>
                  
                  <div className="flex-1">
                    <h4 className="font-bold text-gray-900 dark:text-white text-lg">
                      {testimonial.name}
                    </h4>
                    <p className="text-gray-600 dark:text-gray-300 text-sm">
                      {testimonial.role}
                    </p>
                    <p className="text-primary-600 dark:text-primary-400 text-sm font-medium">
                      {testimonial.university}
                    </p>
                  </div>

                  <div className="flex gap-1">
                    {[...Array(testimonial.rating)].map((_, i) => (
                      <Star key={i} className="w-4 h-4 text-yellow-500 fill-current" />
                    ))}
                  </div>
                </div>

                {/* Content */}
                <blockquote className="text-gray-700 dark:text-gray-300 leading-relaxed mb-6 text-lg">
                  "{testimonial.content}"
                </blockquote>

                {/* Highlight */}
                <div className="inline-flex items-center gap-2 bg-gradient-to-r from-primary-500/10 to-electric-500/10 border border-primary-200/50 dark:border-primary-700/50 px-4 py-2 rounded-full">
                  <div className="w-2 h-2 bg-gradient-to-r from-primary-500 to-electric-500 rounded-full"></div>
                  <span className="text-sm font-semibold text-primary-700 dark:text-primary-300">
                    {testimonial.highlight}
                  </span>
                </div>
              </Card>
            </motion.div>
          ))}
        </motion.div>

        {/* Stats Bar */}
        <motion.div
          className="mt-20 grid grid-cols-1 md:grid-cols-3 gap-8"
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6, delay: 0.4 }}
        >
          {[
            { number: "4.9/5", label: "Average Rating", icon: Star },
            { number: "25K+", label: "Happy Students", icon: Quote },
            { number: "98%", label: "Would Recommend", icon: Star }
          ].map((stat, index) => (
            <motion.div
              key={index}
              className="text-center group"
              initial={{ opacity: 0, scale: 0.8 }}
              whileInView={{ opacity: 1, scale: 1 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: 0.6 + index * 0.1 }}
            >
              <div className="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary-500 to-electric-500 rounded-2xl mb-4 group-hover:scale-110 transition-transform duration-300">
                <stat.icon className="w-8 h-8 text-white" />
              </div>
              <div className="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {stat.number}
              </div>
              <div className="text-gray-600 dark:text-gray-300 font-medium">
                {stat.label}
              </div>
            </motion.div>
          ))}
        </motion.div>
      </div>
    </section>
  );
};

export default TestimonialsSection;
