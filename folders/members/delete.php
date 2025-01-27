<?php

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    session_start();

    $pdo = new PDO('mysql:host=db;dbname=todo', 'root', 'root');

    $folder_id = $_POST['id'];
    $user_id = $_POST['user_id'];

    $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE folder_id = :folder_id AND user_id = :user_id');
    $stmt->execute(['folder_id' => $folder_id, 'user_id' => $_SESSION['user_id']]);
    if ($stmt->fetch()) {
      $stmt = $pdo->prepare('DELETE FROM folders_users WHERE folder_id = :folder_id AND user_id = :user_id');
      $stmt->execute(['folder_id' => $folder_id, 'user_id' => $user_id]);
      $stmt->fetch();
      header('Location: /folders/members.php?id=' . $folder_id);
    } else {
      echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
      echo '<header class="alert-bar">メンバーが見つかりません。</header>';
    }
  }
