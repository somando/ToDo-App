<?php
  session_start();

  if (!isset($_SESSION['user_id'])) {
    echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
    echo '<header class="alert-bar">ToDo Listを利用するにはログインが必要です。<a href="/signin.php">ログイン</a> <a href="/signup.php">新規登録</a></header>';
  } else {
    $pdo = new PDO('mysql:host=db;dbname=todo', 'root', 'root');
  }

  header('Content-Type: application/json; charset=UTF-8');

  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE user_id = :user_id AND folder_id = :folder_id');
    $stmt->execute(['user_id' => $_SESSION['user_id'], 'folder_id' => $_GET['folder_id']]);

    if ($stmt->fetch()) {

      $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE folder_id = :folder_id');
      $stmt->execute(['folder_id' => $_GET['folder_id']]);
      $folders = $stmt->fetchAll();
      foreach ($folders as &$folder) {
        $folder['is_self'] = ($folder['user_id'] === $_SESSION['user_id']);
      }
      echo json_encode($folders);

    } else {

      echo json_encode([]);

    }

  } else {

    echo json_encode([]);

  }
