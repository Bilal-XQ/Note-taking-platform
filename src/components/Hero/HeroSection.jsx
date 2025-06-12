import React from 'react';
import { motion } from 'framer-motion';
import { ArrowRight, Play, ArrowDown } from 'lucide-react';

const HeroSection = () => {
  const stats = [
    { number: "25K+", label: "Students" },
    { number: "1M+", label: "Notes Created" },
    { number: "98%", label: "Better Grades" }
  ];

  const scrollToFeatures = () => {
    const featuresSection = document.getElementById('features');
    if (featuresSection) {
      featuresSection.scrollIntoView({ behavior: 'smooth' });
    }
  };

  return (
    <section className="section bg-gradient-to-b from-primary-50 to-white dark:from-primary-900 dark:to-primary-800 relative overflow-hidden">
      <div className="container">
        <div className="grid grid-cols-1 lg:grid-cols-5 gap-12 items-center min-h-[600px]">
          {/* Content - 60% */}
          <div className="lg:col-span-3">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              className="max-w-2xl"
            >
              {/* Badge */}
              <div className="inline-flex items-center gap-2 bg-accent-100 text-accent-700 px-3 py-1 rounded-full text-sm font-medium mb-6">
                <div className="w-1.5 h-1.5 bg-accent-500 rounded-full"></div>
                Trusted by 25,000+ students
              </div>

              {/* Headline */}
              <h1 className="text-5xl lg:text-6xl font-semibold tracking-tight text-primary-900 dark:text-white mb-6">
                Take better notes.
                <br />
                <span className="text-gradient">Study smarter.</span>
              </h1>

              {/* Description */}
              <p className="text-xl text-primary-600 dark:text-primary-300 mb-8 leading-relaxed">
                Transform your study experience with our intelligent note-taking platform. 
                Organize course materials, boost retention, and achieve academic excellence.
              </p>

              {/* CTAs */}
              <div className="flex flex-col sm:flex-row gap-4 mb-12">
                <button 
                  className="btn btn-primary btn-large"
                  onClick={() => window.location.href = '/main/src/views/student/login.php'}
                >
                  Get started free
                  <ArrowRight className="w-5 h-5" />
                </button>
                <button className="btn btn-secondary btn-large group">
                  <Play className="w-5 h-5 group-hover:scale-110 transition-transform" />
                  Watch demo
                </button>
              </div>

              {/* Stats */}
              <div className="grid grid-cols-3 gap-8">
                {stats.map((stat, index) => (
                  <motion.div
                    key={index}
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6, delay: 0.2 + index * 0.1 }}
                    className="text-center lg:text-left"
                  >
                    <div className="text-2xl lg:text-3xl font-bold text-primary-900 dark:text-white">
                      {stat.number}
                    </div>
                    <div className="text-sm text-primary-600 dark:text-primary-400">
                      {stat.label}
                    </div>
                  </motion.div>
                ))}
              </div>
            </motion.div>
          </div>

          {/* Visual - 40% */}
          <div className="lg:col-span-2">
            <motion.div
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.8, delay: 0.2 }}
              className="relative"
            >
              {/* Main device mockup */}
              <div className="relative bg-white dark:bg-primary-800 rounded-2xl shadow-2xl p-6 mx-auto max-w-sm">
                {/* Device header */}
                <div className="flex items-center gap-2 mb-4">
                  <div className="w-3 h-3 bg-red-400 rounded-full"></div>
                  <div className="w-3 h-3 bg-yellow-400 rounded-full"></div>
                  <div className="w-3 h-3 bg-green-400 rounded-full"></div>
                </div>

                {/* Content preview */}
                <div className="space-y-3">
                  <div className="h-4 bg-primary-100 dark:bg-primary-700 rounded w-3/4"></div>
                  <div className="h-3 bg-primary-100 dark:bg-primary-700 rounded w-full"></div>
                  <div className="h-3 bg-primary-100 dark:bg-primary-700 rounded w-5/6"></div>
                  <div className="h-8 bg-gradient-to-r from-accent-400 to-accent-600 rounded-lg"></div>
                  <div className="space-y-2 pt-2">
                    <div className="h-2 bg-primary-100 dark:bg-primary-700 rounded w-full"></div>
                    <div className="h-2 bg-primary-100 dark:bg-primary-700 rounded w-4/5"></div>
                    <div className="h-2 bg-primary-100 dark:bg-primary-700 rounded w-3/5"></div>
                  </div>
                </div>
              </div>

              {/* Floating elements */}
              <motion.div
                className="absolute -top-4 -right-4 bg-white dark:bg-primary-800 rounded-xl p-3 shadow-lg border border-primary-200 dark:border-primary-700"
                animate={{ y: [0, -8, 0] }}
                transition={{ duration: 3, repeat: Infinity, ease: "easeInOut" }}
              >
                <div className="w-6 h-6 bg-gradient-to-br from-accent-400 to-accent-600 rounded-lg"></div>
              </motion.div>

              <motion.div
                className="absolute -bottom-4 -left-4 bg-white dark:bg-primary-800 rounded-xl p-3 shadow-lg border border-primary-200 dark:border-primary-700"
                animate={{ y: [0, 8, 0] }}
                transition={{ duration: 4, repeat: Infinity, ease: "easeInOut", delay: 1 }}
              >
                <div className="w-6 h-6 bg-gradient-to-br from-green-400 to-emerald-500 rounded-lg"></div>
              </motion.div>
            </motion.div>
          </div>
        </div>

        {/* Scroll Indicator */}
        <motion.div
          className="absolute bottom-8 left-1/2 transform -translate-x-1/2"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 1.5 }}
        >
          <motion.button
            onClick={scrollToFeatures}
            className="flex flex-col items-center gap-2 text-primary-600 dark:text-primary-400 hover:text-primary-900 dark:hover:text-white transition-colors duration-200"
            animate={{ y: [0, 8, 0] }}
            transition={{ duration: 2, repeat: Infinity, ease: "easeInOut" }}
          >
            <span className="text-sm font-medium">Discover More</span>
            <ArrowDown className="w-5 h-5" />
          </motion.button>
        </motion.div>
      </div>

      {/* Background decorations */}
      <div className="absolute inset-0 -z-10">
        <motion.div
          className="absolute top-1/4 left-1/4 w-32 h-32 bg-gradient-to-br from-accent-400/20 to-accent-600/20 rounded-full blur-xl"
          animate={{
            scale: [1, 1.2, 1],
            opacity: [0.3, 0.6, 0.3],
            x: [0, 50, 0],
            y: [0, -30, 0]
          }}
          transition={{ duration: 8, repeat: Infinity, ease: "easeInOut" }}
        />
        <motion.div
          className="absolute top-3/4 right-1/4 w-24 h-24 bg-gradient-to-br from-primary-400/20 to-primary-600/20 rounded-full blur-xl"
          animate={{
            scale: [1, 1.3, 1],
            opacity: [0.2, 0.5, 0.2],
            x: [0, -40, 0],
            y: [0, 20, 0]
          }}
          transition={{ duration: 10, repeat: Infinity, ease: "easeInOut", delay: 2 }}
        />
      </div>
    </section>
  );
};

export default HeroSection;