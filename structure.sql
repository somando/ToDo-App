CREATE DATABASE IF NOT EXISTS todo;

USE todo;

CREATE TABLE IF NOT EXISTS users (
  id VARCHAR(255) NOT NULL PRIMARY KEY,
  password_digest VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS folders (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  description TEXT NULL,
  owner_id VARCHAR(255) NOT NULL,
  FOREIGN KEY (owner_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS folders_users (
  user_id VARCHAR(255) NOT NULL,
  folder_id INT NOT NULL,
  PRIMARY KEY (user_id, folder_id)
);

CREATE TABLE IF NOT EXISTS tasks (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  created_user_id VARCHAR(255) NOT NULL,
  folder_id INT NOT NULL,
  done BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (folder_id) REFERENCES folders(id),
  FOREIGN KEY (created_user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS task_assignments (
  task_id INT NOT NULL,
  user_id VARCHAR(255) NOT NULL,
  PRIMARY KEY (user_id, task_id)
);
