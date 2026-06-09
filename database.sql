-- ============================================
-- RBAC Project Database Setup
-- Run this in phpMyAdmin or MySQL terminal
-- ============================================

CREATE DATABASE IF NOT EXISTS rbac_project CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rbac_project;

-- Roles table
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    label VARCHAR(50) NOT NULL
);

-- Insert the 4 roles
INSERT INTO roles (name, label) VALUES
('super_admin', 'Super Admin'),
('moderator', 'Moderator'),
('regular_user', 'Regular User'),
('guest', 'Guest');

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL DEFAULT 3,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Posts table
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- Seed demo users (password for all: password)
-- ============================================
INSERT INTO users (username, email, password, role_id) VALUES
('superadmin', 'superadmin@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('moderator',  'moderator@demo.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2),
('alice',      'alice@demo.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3),
('bob',        'bob@demo.com',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3),
('guest',      'guest@demo.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4);

-- Seed demo posts
INSERT INTO posts (user_id, title, content) VALUES
(3, 'Welcome to RBAC Demo!', 'This is a demo post by Alice. Regular users can create posts and manage their own content.'),
(4, 'Testing Post Permissions', 'Bob here! I created this post. Only I, moderators, and super admins can delete it.'),
(3, 'How Role-Based Access Works', 'Role-based access control ensures each user only does what they are allowed to do.');

-- Seed demo comments
INSERT INTO comments (post_id, user_id, content) VALUES
(1, 4, 'Great post, Alice! This system looks clean.'),
(1, 3, 'Thanks Bob! Feel free to explore the permissions.'),
(2, 3, 'Nice post Bob! I am commenting as Alice.'),
(3, 4, 'Very informative post!');
