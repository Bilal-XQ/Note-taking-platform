import React from 'react';
import { motion } from 'framer-motion';
import { GraduationCap } from 'lucide-react';

const Footer = () => {
  const footerLinks = [
    { href: '#features', label: 'Features' },
    { href: '#how-it-works', label: 'How it works' },
    { href: '#pricing', label: 'Pricing' },
    { href: '#support', label: 'Support' },
    { href: '#contact', label: 'Contact' }
  ];

  return (
    <footer className="bg-primary-50 dark:bg-primary-900 border-t border-primary-200 dark:border-primary-800">
      <div className="container py-12">
        <div className="flex flex-col md:flex-row justify-between items-center gap-8">
          {/* Brand */}
          <motion.div
            className="flex items-center gap-2"
            initial={{ opacity: 0, x: -20 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
          >
            <div className="w-8 h-8 bg-gradient-to-br from-accent-500 to-accent-600 rounded-lg flex items-center justify-center">
              <GraduationCap className="w-5 h-5 text-white" />
            </div>
            <span className="text-xl font-semibold text-primary-900 dark:text-white">StudyNotes</span>
          </motion.div>

          {/* Navigation */}
          <motion.nav
            className="flex flex-wrap gap-6 justify-center"
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.2 }}
          >
            {footerLinks.map((link, index) => (
              <a
                key={index}
                href={link.href}
                className="text-primary-600 dark:text-primary-300 hover:text-primary-900 dark:hover:text-white font-medium transition-colors duration-200"
              >
                {link.label}
              </a>
            ))}
          </motion.nav>
        </div>

        {/* Copyright */}
        <motion.div
          className="mt-8 pt-8 border-t border-primary-200 dark:border-primary-800 text-center"
          initial={{ opacity: 0 }}
          whileInView={{ opacity: 1 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6, delay: 0.4 }}
        >
          <p className="text-primary-500 dark:text-primary-400">
            &copy; 2025 StudyNotes. All rights reserved.
          </p>
        </motion.div>
      </div>
    </footer>
    );
};

export default Footer;
