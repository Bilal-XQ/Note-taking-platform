import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { GraduationCap, Menu, X } from 'lucide-react';

const Navbar = () => {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [isScrolled, setIsScrolled] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 50);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const navLinks = [
    { href: '#features', label: 'Features' },
    { href: '#how-it-works', label: 'How it works' },
    { href: '#pricing', label: 'Pricing' },
    { href: '#support', label: 'Support' }
  ];

  const scrollToSection = (href) => {
    const element = document.querySelector(href);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' });
    }
    setIsMobileMenuOpen(false);
  };

  return (
    <motion.header
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        isScrolled 
          ? 'bg-white/95 dark:bg-primary-900/95 backdrop-blur-md border-b border-primary-200 dark:border-primary-800' 
          : 'bg-transparent'
      }`}
      initial={{ y: -100 }}
      animate={{ y: 0 }}
      transition={{ duration: 0.6 }}
    >
      <div className="container">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 bg-gradient-to-br from-accent-500 to-accent-600 rounded-lg flex items-center justify-center">
              <GraduationCap className="w-5 h-5 text-white" />
            </div>
            <span className="text-xl font-semibold text-primary-900 dark:text-white">
              StudyNotes
            </span>
          </div>

          {/* Desktop Navigation */}
          <nav className="hidden md:flex items-center gap-8">
            {navLinks.map((link) => (
              <button
                key={link.href}
                onClick={() => scrollToSection(link.href)}
                className="text-primary-600 dark:text-primary-300 hover:text-primary-900 dark:hover:text-white font-medium transition-colors duration-200"
              >
                {link.label}
              </button>
            ))}
          </nav>

          {/* Desktop CTA */}
          <div className="hidden md:flex items-center gap-4">
            <button 
              className="text-primary-600 dark:text-primary-300 hover:text-primary-900 dark:hover:text-white font-medium"
              onClick={() => window.location.href = '/main/src/views/student/login.php'}
            >
              Sign in
            </button>
            <button 
              className="btn btn-primary"
              onClick={() => window.location.href = '/main/src/views/student/login.php'}
            >
              Get started
            </button>
          </div>

          {/* Mobile Menu Button */}
          <button
            className="md:hidden p-2"
            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
          >
            {isMobileMenuOpen ? (
              <X className="w-6 h-6 text-primary-900 dark:text-white" />
            ) : (
              <Menu className="w-6 h-6 text-primary-900 dark:text-white" />
            )}
          </button>
        </div>

        {/* Mobile Menu */}
        {isMobileMenuOpen && (
          <motion.div
            className="md:hidden border-t border-primary-200 dark:border-primary-800 bg-white dark:bg-primary-900"
            initial={{ opacity: 0, height: 0 }}
            animate={{ opacity: 1, height: 'auto' }}
            exit={{ opacity: 0, height: 0 }}
          >
            <div className="py-4 space-y-4">
              {navLinks.map((link) => (
                <button
                  key={link.href}
                  onClick={() => scrollToSection(link.href)}
                  className="block w-full text-left px-4 py-2 text-primary-600 dark:text-primary-300 hover:text-primary-900 dark:hover:text-white font-medium"
                >
                  {link.label}
                </button>
              ))}
              <div className="border-t border-primary-200 dark:border-primary-800 pt-4 px-4 space-y-3">
                <button 
                  className="block w-full text-left text-primary-600 dark:text-primary-300 font-medium"
                  onClick={() => window.location.href = '/main/src/views/student/login.php'}
                >
                  Sign in
                </button>
                <button 
                  className="btn btn-primary w-full"
                  onClick={() => window.location.href = '/main/src/views/student/login.php'}
                >
                  Get started
                </button>
              </div>
            </div>
          </motion.div>
        )}
      </div>
    </motion.header>
  );
};

export default Navbar;
