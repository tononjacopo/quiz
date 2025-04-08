# 🧠 PHP Quiz Web Application

[![Build Status](https://img.shields.io/badge/build-passing-brightgreen)](https://github.com/tononjacopo/quiz/actions)
[![MIT License](https://img.shields.io/badge/license-MIT-blue)](LICENSE)
[![Version](https://img.shields.io/badge/version-1.1.7-orange)](https://github.com/tononjacopo/quiz/releases)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-8892BF)](https://www.php.net/)

A complete quiz management system for educational institutions developed in PHP. It allows teachers to create quizzes and students to answer questions, all through a centralized admin interface.

## 🌐 Online Demo

Try the demo here: [PHP Quiz App](https://www.tononjacopo.com/quiz/)

## 🔥 Features

- ✅ **Multi-User System** – Three access levels: administrator, teacher, and student. 👥  
- ✅ **Admin Panel** – Manage users, classes, and monitor activity. 🔧  
- ✅ **Teacher Interface** – Create quizzes with open-ended questions and review responses. 👨‍🏫  
- ✅ **Student Portal** – View and complete assigned quizzes. 👨‍🎓  
- ✅ **Duplicate Access Prevention** – Students cannot access the same quiz multiple times. 🔒  
- ✅ **Responsive Design** – Works on all devices, from desktop to mobile. 📱💻

## 📸 Screenshots

### 🔹 Admin Panel

<img src="https://github.com/tononjacopo/quiz/blob/cf1a49d833c8052cbb15bf91abb9f04f26457de3/screenshot/admin.png" width="700">

*Admin panel with user and class management.*

### 🔹 Teacher Interface

<img src="https://github.com/tononjacopo/quiz/blob/cf1a49d833c8052cbb15bf91abb9f04f26457de3/screenshot/ins.png" width="700">

*Teacher panel for quiz creation and review.*

### 🔹 Student Portal

<img src="https://github.com/tononjacopo/quiz/blob/cf1a49d833c8052cbb15bf91abb9f04f26457de3/screenshot/stud.png" width="700">

*Student interface for viewing and completing quizzes.*

## 🗁 Project Structure

```plaintext
📂 quiz-system
├── 📁 auth/               # Authentication management
│   ├── login.php          # Login process
│   └── logout.php         # Logout process
├── 📁 config/             # Configuration files
│   ├── local.php          # Local config
│   └── database.php       # Database connection settings
├── 📁 modules/            # Application modules
│   ├── admin/             # Admin features
│   │   └── admin.php      # Admin dashboard
│   ├── teacher/           # Teacher features
│   │   ├── teacher.php    # Teacher dashboard
│   │   ├── edit_quiz.php  # Edit quiz
│   │   └── quiz_deails.php# Quiz details (student answers)
│   └── student/           # Student features
│       └── student.php    # Student dashboard
├── 📁 assets/             # Static assets
│   ├── css/               # Stylesheets
│   │   ├── admin_style.css# Admin style
│   │   ├── style_teacher.css# Teacher style
│   │   └── style.css      # General style
│   └── img/               # Images and icons
├── 📝 index.php           # Application entry point
└── 📄 LICENSE             # MIT license file
```

## 🛠️ Technologies Used

- **PHP** – Back-end programming language for application logic  
- **MySQL/MariaDB** – Relational database for data storage  
- **HTML5 & CSS3** – Semantic structure and UI styling  
- **JavaScript** – Client-side interactivity and form validation  
- **Simple CSS** – Lightweight CSS framework for responsive design

## 📊 Database Schema

The system is built on several key tables:
- `utente` - User management with different access levels (admin, teacher, student)  
- `classi` - School class management  
- `quiz` - Quiz archive created by teachers  
- `domande` - Questions associated with quizzes  
- `risposte` - Student answers to questions

## 🔒 Security Features

- Passwords hashed in the database  
- Protection against SQL Injection via prepared statements  
- Input validation and sanitization  
- Session control to prevent unauthorized access  
- Automatic redirection for already completed quizzes

## 📩 Contact

- [🌐 Portfolio](https://tononjacopo.com)  
- [🔗 LinkedIn](https://it.linkedin.com/in/tononjacopo)  
- [💡 LeetCode](https://leetcode.com/tononjacopo)  
- [❌ X](https://x.com/devtononjacopo)  
- [🎨 Dribbble](https://dribbble.com/tononjacopo)

📩 **Email**: [info@tononjacopo.com](mailto:info@tononjacopo.com)

## 📝 License

This project is distributed under the **MIT** license. You are free to use, modify, and distribute it! 🚀

---

**🔗 [Check out the Demo](https://www.tononjacopo.com/quiz/) and leave a ⭐ on GitHub if you like it!** 😊✨
