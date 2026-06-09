<div align="center">

# ⬡ RBAC App
### Role-Based Access Control System

<br/>

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)

<br/>

> A full-stack Role-Based Access Control web application built with **PHP**, **MySQL**, **HTML/CSS**, and **Vanilla JavaScript** — featuring 4 distinct user roles, session-based authentication, and a real-time commenting system.

<br/>

![RBAC App Demo](https://raw.githubusercontent.com/YOUR_USERNAME/rbac_project/main/assets/demo/demo.gif)

> 📌 *Replace the GIF above with a screen recording of your app using [ScreenToGif](https://www.screentogif.com/) — record the login, post creation, and permission flow.*

</div>

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Role & Permission Matrix](#-role--permission-matrix)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Getting Started](#-getting-started)
- [Database Schema](#-database-schema)
- [Screenshots](#-screenshots)
- [Demo Accounts](#-demo-accounts)
- [Author](#-author)

---

## 🔍 Overview

This project demonstrates a complete **Role-Based Access Control (RBAC)** system where different users have different levels of access to resources. Built as a hiring assessment for a Full Stack Developer role, it showcases:

- Secure **session-based authentication** with password hashing
- A **4-tier role hierarchy** enforced on both frontend and backend
- **Real-time AJAX** interactions for comments without page reload
- Clean **MVC-inspired** architecture using PHP includes and a centralized permission layer
- A **colorful, modern UI** built entirely with vanilla CSS — no frameworks

---

## ✨ Features

- 🔐 **Authentication** — Register, login, logout with `password_hash` / `password_verify`
- 👥 **4 User Roles** — Super Admin, Moderator, Regular User, Guest
- 📝 **Posts** — Create, edit, delete with ownership enforcement
- 💬 **Comments** — Add and delete with fine-grained permission logic
- ⚡ **AJAX Comments** — Live add/delete without page refresh
- 🛡️ **User Management** — Super Admin can change any user's role or delete them
- 🎨 **Modern UI** — Gradient accents, role badges, toast notifications, confirm modals
- 📱 **Responsive** — Works on desktop and mobile

---

## 🔐 Role & Permission Matrix

| Action | Super Admin | Moderator | Regular User | Guest |
|---|:---:|:---:|:---:|:---:|
| View posts & comments | ✅ | ✅ | ✅ | ✅ |
| Create a post | ✅ | ✅ | ✅ | ❌ |
| Edit own post | ✅ | ❌ | ✅ | ❌ |
| Delete any post | ✅ | ✅ | ❌ | ❌ |
| Delete own post | ✅ | ✅ | ✅ | ❌ |
| Create a comment | ✅ | ✅ | ✅ | ❌ |
| Delete own comment | ✅ | ✅ | ✅ | ❌ |
| Delete comment on own post | ✅ | ✅ | ✅ | ❌ |
| Delete any comment | ✅ | ✅ | ❌ | ❌ |
| Manage users (roles/delete) | ✅ | ❌ | ❌ | ❌ |

### Comment Permission Logic

```
If User A creates a post and User B comments on it:
  ✅ User A  (post owner)    → can delete User B's comment
  ✅ User B  (comment owner) → can delete their own comment
  ❌ User C  (anyone else)   → cannot delete User B's comment
  ✅ Moderator               → can delete any comment
  ✅ Super Admin             → can delete anything
```

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | PHP 8.x (plain, no framework) |
| **Database** | MySQL 8 via PDO |
| **Frontend** | HTML5, CSS3, Vanilla JavaScript |
| **Auth** | PHP Sessions + `password_hash` (bcrypt) |
| **Server** | Apache via XAMPP |
| **Fonts** | Google Fonts — Inter, Space Grotesk |

---

## 📁 Project Structure

```
rbac_project/
│
├── index.php                  # Root redirect
├── database.sql               # Full DB schema + seed data
├── README.md
│
├── includes/                  # Shared core modules
│   ├── db.php                 # PDO database connection (singleton)
│   ├── auth.php               # Session helpers + permission functions
│   ├── header.php             # Global navbar HTML
│   └── footer.php             # Global footer HTML
│
├── pages/                     # User-facing pages
│   ├── login.php              # Login form + session creation
│   ├── register.php           # Registration (assigns Regular User role)
│   ├── logout.php             # Session destroy + redirect
│   ├── posts.php              # Main feed — posts + comments
│   ├── edit_post.php          # Edit post (owner / super admin only)
│   └── users.php              # User management (Super Admin only)
│
├── api/                       # AJAX JSON endpoints
│   ├── create_post.php        # POST → insert new post
│   ├── delete_post.php        # POST → delete post (permission checked)
│   ├── add_comment.php        # POST → insert comment, returns JSON
│   └── delete_comment.php     # POST → delete comment (permission checked)
│
└── assets/
    ├── css/
    │   └── style.css          # Full custom design system
    └── js/
        └── main.js            # AJAX helpers, toast, confirm modal
```

---

## 🚀 Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP 8.x)
- [VS Code](https://code.visualstudio.com/)
- [Git](https://git-scm.com/)

### Installation

**1. Clone the repository**

```bash
git clone https://github.com/YOUR_USERNAME/rbac_project.git
```

**2. Move to XAMPP's web root**

```
C:\xampp\htdocs\rbac_project\
```

**3. Start XAMPP services**

Open **XAMPP Control Panel** → Start **Apache** and **MySQL**

**4. Import the database**

- Go to `http://localhost/phpmyadmin`
- Create a new database named `rbac_project`
- Click **Import** → select `database.sql` → click **Go**

**5. Configure database credentials** *(if needed)*

Open `includes/db.php` and update if your MySQL has a password:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');        // Add your password here if set
define('DB_NAME', 'rbac_project');
```

**6. Open the app**

```
http://localhost/rbac_project
```

---

## 🗄️ Database Schema

```sql
users      → id, username, email, password (bcrypt), role_id, created_at
roles      → id, name, label
posts      → id, user_id, title, content, created_at, updated_at
comments   → id, post_id, user_id, content, created_at
```

**Relationships:**
- `users.role_id` → `roles.id`
- `posts.user_id` → `users.id` (CASCADE DELETE)
- `comments.post_id` → `posts.id` (CASCADE DELETE)
- `comments.user_id` → `users.id` (CASCADE DELETE)

---

## 📸 Screenshots

> 📌 *Add your own screenshots after running the project locally.*
> Recommended: capture Login, Posts Feed, User Management, and mobile view.

| Login Page | Posts Feed |
|---|---|
| ![Login](assets/demo/login.png) | ![Posts](assets/demo/posts.png) |

| User Management | Comment Permissions |
|---|---|
| ![Users](assets/demo/users.png) | ![Comments](assets/demo/comments.png) |

*To add screenshots: create an `assets/demo/` folder and place your images there.*

---

## 🔑 Demo Accounts

All demo accounts use the password: **`password`**

| Username | Role | Access Level |
|---|---|---|
| `superadmin` | Super Admin | Full access — delete anything, manage all users |
| `moderator` | Moderator | Delete any post or comment, cannot manage users |
| `alice` | Regular User | Create posts/comments, manage only own content |
| `bob` | Regular User | Same as Alice — useful for testing comment permissions |
| `guest` | Guest | View-only — cannot post or comment |

---

## 👤 Author

**Tansiv Jubayer**

[![GitHub](https://img.shields.io/badge/GitHub-100000?style=for-the-badge&logo=github&logoColor=white)](https://github.com/YOUR_USERNAME)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-0077B5?style=for-the-badge&logo=linkedin&logoColor=white)](https://linkedin.com/in/YOUR_PROFILE)
[![Email](https://img.shields.io/badge/Email-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:jtansiv@gmail.com)

---

<div align="center">

Built with 💜 as a Full Stack Developer assessment task.

⭐ If you found this helpful, consider starring the repository!

</div>
