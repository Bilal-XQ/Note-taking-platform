# React Development Guidelines

## Component Architecture

### Component Types
- **UI Components**: Pure, reusable components (Button, Card, Input)
- **Feature Components**: Business logic components (NoteEditor, QuizGenerator)
- **Layout Components**: Page structure components (Header, Sidebar, Footer)
- **Page Components**: Top-level route components

### Component Structure
```jsx
import React from 'react';
import { motion } from 'framer-motion';
import { SomeIcon } from 'lucide-react';

const ComponentName = ({ 
  prop1, 
  prop2 = 'defaultValue',
  className = '',
  ...props 
}) => {
  // Hooks at the top
  const [state, setState] = useState(null);
  
  // Event handlers
  const handleClick = () => {
    // Implementation
  };
  
  // Render
  return (
    <div className={`base-classes ${className}`} {...props}>
      {/* Component content */}
    </div>
  );
};

export default ComponentName;
```

### Props Guidelines
- Use **destructuring** for props
- Provide **default values** for optional props
- Use **rest/spread** for passing through props
- Document complex prop types with comments

### State Management
- Use **useState** for local component state
- Use **useContext** for shared state across components
- Keep state as **close to usage** as possible
- Avoid **prop drilling** beyond 2-3 levels

### Performance Optimization
- Use **React.memo** for expensive pure components
- Use **useMemo** for expensive calculations
- Use **useCallback** for stable function references
- Implement **proper dependency arrays** in hooks

## Animation Guidelines

### Framer Motion Best Practices
```jsx
// Animation variants
const fadeInUp = {
  initial: { opacity: 0, y: 20 },
  animate: { opacity: 1, y: 0 },
  transition: { duration: 0.6, ease: "easeOut" }
};

// Stagger animations for lists
const staggerContainer = {
  animate: {
    transition: {
      staggerChildren: 0.1
    }
  }
};
```

### Animation Principles
- Keep animations **subtle and purposeful**
- Use **consistent timing** (0.2s for micro, 0.6s for macro)
- Implement **reduced motion** support
- Test on **lower-end devices**

## Styling Guidelines

### Tailwind CSS Usage
- Use **semantic class names** for complex components
- Group related classes together
- Use **responsive prefixes** consistently
- Leverage **custom CSS properties** for dynamic values

### Component Styling Patterns
```jsx
// Base + variant pattern
const buttonClasses = {
  base: "inline-flex items-center justify-center font-medium transition-colors",
  variants: {
    primary: "bg-primary-600 text-white hover:bg-primary-700",
    secondary: "bg-white text-primary-600 border border-primary-300"
  }
};
```

## Accessibility Guidelines

### Semantic HTML
- Use **proper heading hierarchy** (h1 → h2 → h3)
- Use **landmark elements** (main, nav, section, article)
- Provide **meaningful alt text** for images
- Use **button** elements for interactions

### ARIA Implementation
```jsx
// Proper ARIA usage
<button
  aria-label="Close dialog"
  aria-expanded={isOpen}
  aria-controls="dialog-content"
  onClick={handleClose}
>
  <X className="w-4 h-4" />
</button>
```

### Keyboard Navigation
- Ensure **tab order** is logical
- Provide **focus indicators**
- Handle **escape key** for modals
- Support **arrow keys** for navigation

## Error Handling

### Error Boundaries
```jsx
class ErrorBoundary extends React.Component {
  constructor(props) {
    super(props);
    this.state = { hasError: false };
  }

  static getDerivedStateFromError(error) {
    return { hasError: true };
  }

  componentDidCatch(error, errorInfo) {
    console.error('Error caught by boundary:', error, errorInfo);
  }

  render() {
    if (this.state.hasError) {
      return <ErrorFallback />;
    }

    return this.props.children;
  }
}
```

### Async Error Handling
```jsx
const [loading, setLoading] = useState(false);
const [error, setError] = useState(null);

const handleAsyncAction = async () => {
  setLoading(true);
  setError(null);
  
  try {
    await someAsyncOperation();
  } catch (err) {
    setError(err.message);
  } finally {
    setLoading(false);
  }
};
```

## Testing Guidelines

### Component Testing
```jsx
import { render, screen, fireEvent } from '@testing-library/react';
import Button from './Button';

describe('Button', () => {
  it('renders with correct text', () => {
    render(<Button>Click me</Button>);
    expect(screen.getByRole('button')).toHaveTextContent('Click me');
  });

  it('calls onClick handler', () => {
    const handleClick = jest.fn();
    render(<Button onClick={handleClick}>Click me</Button>);
    
    fireEvent.click(screen.getByRole('button'));
    expect(handleClick).toHaveBeenCalledTimes(1);
  });
});
```

### Custom Hook Testing
```jsx
import { renderHook, act } from '@testing-library/react';
import { useCounter } from './useCounter';

describe('useCounter', () => {
  it('should increment counter', () => {
    const { result } = renderHook(() => useCounter());
    
    act(() => {
      result.current.increment();
    });
    
    expect(result.current.count).toBe(1);
  });
});
```

## File Organization

### Import Order
1. React and React-related imports
2. Third-party library imports
3. Internal utility imports
4. Component imports
5. Type/interface imports (if using TypeScript)

```jsx
import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { formatDate } from '../utils/date';
import Button from './Button';
```

### Export Patterns
```jsx
// Named exports for utilities
export { formatDate, parseDate };

// Default export for components
export default Button;

// Re-exports for barrel exports
export { default as Button } from './Button';
export { default as Card } from './Card';
```
