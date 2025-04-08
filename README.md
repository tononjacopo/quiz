# 🧠 Quiz Application

[![Build Status](https://img.shields.io/badge/build-passing-brightgreen)](https://github.com/tononjacopo/quiz/actions)
[![MIT License](https://img.shields.io/badge/license-MIT-blue)](LICENSE)
[![Version](https://img.shields.io/badge/version-1.1.7-orange)](https://github.com/tononjacopo/quiz/releases)

An interactive and engaging quiz application that allows users to test their knowledge on various topics with a clean and user-friendly interface.

## 🌐 Live Demo

Check out the live demo here: [Quiz Application](https://www.tononjacopo.com/quiz/)

## 🔥 Features

- ✅ **User-Friendly Interface** – Intuitive design making it easy to navigate and answer quiz questions. 🏆
- ✅ **Multiple Question Categories** – Wide range of topics to challenge users with diverse questions. 📚
- ✅ **Score Tracking** – Keeps track of user scores and provides feedback on performance. 🎯
- ✅ **Responsive Design** – Works seamlessly across all devices, from desktops to smartphones. 📱💻

## 📸 Screenshots

### 🔹 Admin

<img src="https://github.com/tononjacopo/quiz/blob/cf1a49d833c8052cbb15bf91abb9f04f26457de3/screenshot/admin.png" width="700">

*Admin panel.*


### 🔹 Teachers Page

<img src="https://github.com/tononjacopo/quiz/blob/cf1a49d833c8052cbb15bf91abb9f04f26457de3/screenshot/ins.png" width="700">

*Teachers panel.*


### 🔹 Students Page

<img src="https://github.com/tononjacopo/quiz/blob/cf1a49d833c8052cbb15bf91abb9f04f26457de3/screenshot/stud.png" width="700">

*A clean and engaging quiz interface that guides users through the questions.*

## 🗁 Project Structure

```plaintext
📂 quiz-system
├── 📁 auth/               # Gestione autenticazione
│   ├── login.php          # Processo di login
│   └── logout.php         # Processo di logout
├── 📁 config/             # File di configurazione
│   ├── local.php          # config locale
│   └── database.php       # Configurazione connessione al database
├── 📁 modules/            # Moduli applicazione
│   ├── admin/             # Funzionalità amministratore
│   │   └── admin.php      # Dashboard amministratore
│   ├── teacher/           # Funzionalità docente
│   │   ├── teacher.php    # Dashboard docente
│   │   ├── edit_quiz.php  # Modifica quiz
│   │   └── quiz_deails.php# Dettagli quiz(risposte studenti)
│   └── student/           # Funzionalità studente
│       └── student.php    # Dashboard studente
├── 📁 assets/             # Risorse statiche
│   ├── css/               # Fogli di stile
│   │   ├── admin_style.css# Stile css
│   │   ├── style_teacher.css# Stile insegnante
│   │   └── style.css      # Stile generale
│   └── img/               # Immagini e icone
├── 📝 index.php           # Punto di ingresso dell'applicazione
└── 📄 LICENSE             # File della licenza MIT
```

## 🛠️ Technologies Used

- 🏗️ **HTML5** – Provides the semantic structure of the webpage, ensuring accessibility and SEO optimization.
- 🎨 **CSS3** – Used for styling the page, including layout design, animations, and responsiveness.
- ⚡ **JavaScript (ES6+)** – Handles quiz logic, user interactions, and dynamic score calculations.

## 🚀 Installation

To run this project locally, follow these steps:

1. **Clone the repository**

   ```bash
   git clone https://github.com/tononjacopo/quiz.git
   ```

2. **Navigate to the project directory**

   ```bash
   cd quiz
   ```

3. **Open the `index.html` file in your browser**

   - Option 1: Drag and drop the `index.html` file into your browser window.
   - Option 2: Use a live server extension in your code editor (e.g., VSCode) to view the page locally.

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
