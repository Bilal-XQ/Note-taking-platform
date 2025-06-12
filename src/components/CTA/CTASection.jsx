import React from 'react';
import { motion } from 'framer-motion';
import { ArrowRight } from 'lucide-react';

const CTASection = () => {
  return (
    <section className="section bg-primary-900 dark:bg-primary-800">
      <div className="container text-center">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="max-w-3xl mx-auto"
        >
          {/* Heading */}
          <h2 className="text-4xl lg:text-5xl font-semibold tracking-tight text-white mb-6">
            Ready to transform your learning?
          </h2>

          {/* Description */}
          <p className="text-xl text-primary-300 mb-8 leading-relaxed">
            Join thousands of students who have already improved their grades with StudyNotes.
          </p>

          {/* CTA Button */}
          <motion.button
            className="bg-white text-primary-900 px-8 py-4 rounded-lg font-semibold text-lg inline-flex items-center gap-2 hover:bg-primary-50 transition-colors duration-200"
            whileHover={{ scale: 1.05 }}
            whileTap={{ scale: 0.95 }}
            onClick={() => window.location.href = '/main/src/views/student/login.php'}
          >
            Get started free
            <ArrowRight className="w-5 h-5" />
          </motion.button>

          {/* Trust indicators */}
          <div className="flex flex-col sm:flex-row gap-6 justify-center items-center mt-8 text-sm text-primary-400">
            <div className="flex items-center gap-2">
              <div className="w-1.5 h-1.5 bg-success-500 rounded-full"></div>
              Free forever
            </div>
            <div className="flex items-center gap-2">
              <div className="w-1.5 h-1.5 bg-success-500 rounded-full"></div>
              No credit card required
            </div>
            <div className="flex items-center gap-2">
              <div className="w-1.5 h-1.5 bg-success-500 rounded-full"></div>
              2-minute setup
            </div>
          </div>
        </motion.div>
      </div>
    </section>
  );
};

export default CTASection;
