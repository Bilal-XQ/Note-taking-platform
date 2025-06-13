# StudyNotes - AI-Powered Note-Taking Platform

<div align="center">
  <img src="https://via.placeholder.com/150x150/3B82F6/FFFFFF?text=📚" alt="StudyNotes" width="150"/>
  
  **Transform your study experience with AI-powered note organization**
  
  [![React](https://img.shields.io/badge/React-18.x-blue.svg)](https://reactjs.org/)
  [![PHP](https://img.shields.io/badge/PHP-8.0+-purple.svg)](https://php.net/)
  [![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com/)
  [![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
</div>

## What is StudyNotes?

A modern web application that helps students organize their academic materials using AI. Create notes, get smart summaries, and generate quizzes automatically.

## ✨ Key Features

- 🎯 **AI-Powered Organization** - Smart categorization using Gemini AI
- 📚 **Course Management** - Organize by subjects and modules  
- 🤖 **Smart Summaries** - Automated note summarization
- 📝 **Quiz Generation** - AI-generated quizzes from your notes
- 🔍 **Advanced Search** - Intelligent content discovery
- 🌙 **Modern Interface** - Responsive design with dark mode

## 📸 **Screenshots & Features**

<details>
<summary><strong>🖼️ Click to View Screenshots</strong></summary>

### Landing Page
![Landing Page](https://via.placeholder.com/800x450/3B82F6/FFFFFF?text=Modern+Landing+Page+with+Hero+Section)
*Clean, modern interface with call-to-action and feature highlights*

### Dashboard Overview
![Dashboard](https://via.placeholder.com/800x450/10B981/FFFFFF?text=Student+Dashboard+with+Course+Overview)
*Comprehensive dashboard showing courses, recent notes, and study progress*

### Note Editor
![Note Editor](https://via.placeholder.com/800x450/8B5CF6/FFFFFF?text=Rich+Text+Note+Editor+with+AI+Features)
*Rich text editor with AI-powered suggestions and formatting tools*

### AI-Generated Summary
![AI Summary](https://via.placeholder.com/800x450/F59E0B/FFFFFF?text=AI+Generated+Note+Summary)
*Intelligent note summarization powered by Gemini AI*

### Quiz Generation
![Quiz Interface](https://via.placeholder.com/800x450/EF4444/FFFFFF?text=Auto-Generated+Quiz+Interface)
*Automated quiz creation from your notes for self-assessment*

### Course Management
![Course Management](https://via.placeholder.com/800x450/06B6D4/FFFFFF?text=Course+and+Module+Organization)
*Organized course structure with modules and note categorization*

### Search & Filter
![Search Interface](https://via.placeholder.com/800x450/84CC16/FFFFFF?text=Advanced+Search+with+Smart+Filtering)
*Powerful search functionality with intelligent content discovery*

### Mobile Responsive
![Mobile View](https://via.placeholder.com/400x600/EC4899/FFFFFF?text=Mobile+Responsive+Design)
*Fully responsive design optimized for mobile devices*

</details>

## 🛠️ Tech Stack

**Frontend:** React 18, Tailwind CSS, Framer Motion  
**Backend:** PHP 8+, MySQL 8.0+, Gemini AI API  
**Tools:** Composer, npm, WAMP/XAMPP

## 🚀 Quick Start

### Prerequisites
- Node.js 18.0+
- PHP 8.0+ 
- MySQL 8.0+
- Composer

### Installation

1. **Clone & Install**
   ```bash
   git clone https://github.com/Bilal-XQ/Note-taking-platform.git
   cd Note-taking-platform
   npm install
   composer install
   ```

2. **Setup Environment**
   ```bash
   cp .env.example .env
   ```

3. **Configure API Key**
   
   ⚠️ **Important:** Get your Gemini API key from [Google AI Studio](https://makersuite.google.com/app/apikey)
   
   ```env
   # Edit .env file
   GEMINI_API_KEY=your_actual_api_key_here
   DB_HOST=localhost
   DB_NAME=studynotes
   DB_USER=your_db_user
   DB_PASS=your_db_password
   ```

4. **Setup Database**
   ```bash
   mysql -u root -p -e "CREATE DATABASE studynotes;"
   ```

5. **Start Development**
   ```bash
   # Frontend (port 3002)
   npm start
   
   # Backend (port 8000) 
   php -S localhost:8000
   ```

Visit `http://localhost:3002` to see your application! 🎉

## 📁 Project Structure

```
studynotes/
├── src/
│   ├── components/     # React UI components
│   ├── controllers/    # PHP business logic
│   ├── models/        # Data layer
│   └── views/         # PHP templates
├── public/            # Static assets
└── config/           # Configuration
```

## 🔐 Security Notes

- ✅ API keys use environment variables
- ✅ `.env` file is ignored by Git
- ✅ Only placeholder values in repository
- ⚠️ **Never commit real API keys**

## 🤝 Contributing

1. Fork the repository
2. Create feature branch: `git checkout -b feature/name`
3. Make changes and test
4. Submit pull request

## 📝 License

MIT License - see [LICENSE](LICENSE) file

## 🆘 Need Help?

- 🐛 **Issues:** [GitHub Issues](https://github.com/Bilal-XQ/Note-taking-platform/issues)
- 📖 **Full Docs:** [PROJECT_INSTRUCTIONS.md](PROJECT_INSTRUCTIONS.md)

---

<div align="center">
  <p>Built with ❤️ for students everywhere</p>
</div>
