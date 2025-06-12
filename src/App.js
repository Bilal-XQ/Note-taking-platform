import React from 'react';
import Navbar from './components/Navigation/Navbar';
import HeroSection from './components/Hero/HeroSection';
import FeatureGrid from './components/Features/FeatureGrid';
import HowItWorksSection from './components/HowItWorks/HowItWorksSection';
import CTASection from './components/CTA/CTASection';
import Footer from './components/Footer/Footer';

function App() {
  return (
    <div className="min-h-screen bg-white dark:bg-primary-900 transition-colors duration-300">
      <Navbar />
      <main className="pt-16">
        <HeroSection />
        <FeatureGrid />
        <HowItWorksSection />
        <CTASection />
      </main>
      <Footer />
    </div>
  );
}

export default App;
