# 🧠 PHP Quiz Management System

[![Build Status](https://img.shields.io/badge/build-passing-brightgreen)](https://github.com/tononjacopo/quiz/actions)
[![MIT License](https://img.shields.io/badge/license-MIT-blue)](LICENSE)
[![Version](https://img.shields.io/badge/version-1.1.7-orange)](https://github.com/tononjacopo/quiz/releases)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-8892BF)](https://www.php.net/)

Un sistema completo di gestione quiz per istituti scolastici sviluppato in PHP, che permette a docenti di creare quiz e a studenti di rispondere alle domande, con un'interfaccia amministrativa centrale.

## 🌐 Demo Online

Prova la demo qui: [PHP Quiz System](https://www.tononjacopo.com/quiz/)

## 🔥 Funzionalità

- ✅ **Sistema Multi-Utente** – Tre livelli di accesso: amministratore, docente e studente. 👥
- ✅ **Pannello Amministrativo** – Per gestire utenti, classi e monitorare l'attività. 🔧
- ✅ **Interfaccia Docente** – Creazione di quiz con domande a risposta aperta e revisione delle risposte. 👨‍🏫
- ✅ **Portale Studente** – Visualizzazione e completamento dei quiz assegnati. 👨‍🎓
- ✅ **Prevenzione Accesso Duplicato** – Gli studenti non possono accedere più volte allo stesso quiz. 🔒
- ✅ **Design Responsivo** – Funziona su tutti i dispositivi, dal desktop al mobile. 📱💻

## 📸 Screenshots

### 🔹 Pannello Amministratore

<img src="https://github.com/tononjacopo/quiz/blob/cf1a49d833c8052cbb15bf91abb9f04f26457de3/screenshot/admin.png" width="700">

*Pannello amministrativo con gestione utenti e classi.*

### 🔹 Interfaccia Docente

<img src="https://github.com/tononjacopo/quiz/blob/cf1a49d833c8052cbb15bf91abb9f04f26457de3/screenshot/ins.png" width="700">

*Pannello docente per la creazione e revisione dei quiz.*

### 🔹 Portale Studente

<img src="https://github.com/tononjacopo/quiz/blob/cf1a49d833c8052cbb15bf91abb9f04f26457de3/screenshot/stud.png" width="700">

*Interfaccia studente per la visualizzazione e compilazione dei quiz.*

## 🗁 Struttura del Progetto

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

## 🛠️ Tecnologie Utilizzate

- **PHP** – Linguaggio di programmazione back-end per la logica dell'applicazione.
- **MySQL/MariaDB** – Database relazionale per la memorizzazione dei dati.
- **HTML5 & CSS3** – Struttura semantica e stile dell'interfaccia utente.
- **JavaScript** – Interattività lato client e validazione form.
- **Simple CSS** – Framework CSS leggero per il design responsivo.

## 📊 Schema Database

Il sistema si basa su diverse tabelle chiave:
- `utente` - Gestione utenti con differenti livelli di accesso (amministratore, docente, studente)
- `classi` - Gestione delle classi scolastiche
- `quiz` - Archivio dei quiz creati dai docenti
- `domande` - Domande associate ai quiz
- `risposte` - Risposte degli studenti alle domande


```markdown
## 🔒 Caratteristiche di sicurezza

- Password hashate nel database
- Protezione contro SQL Injection tramite prepared statements
- Validazione input e sanitizzazione
- Controllo sessioni per prevenire accessi non autorizzati
- Reindirizzamento automatico per quiz già completati dagli studenti
```

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
