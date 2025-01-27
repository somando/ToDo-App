<?php

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    session_start();

    $pdo = new PDO('mysql:host=db;dbname=todo', 'root', 'root');

    $task_id = $_POST['id'];

    $stmt = $pdo->prepare('SELECT folder_id FROM tasks WHERE id = :id');
    $stmt->execute(['id' => $task_id]);
    $task = $stmt->fetch();
    if (!$task) {
      echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
      echo '<header class="alert-bar">タスクが見つかりません。</header>';
    } else {
      $folder_id = $task['folder_id'];
      $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE folder_id = :folder_id AND user_id = :user_id');
      $stmt->execute(['folder_id' => $folder_id, 'user_id' => $_SESSION['user_id']]);
      if ($stmt->fetch()) {
        $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = :id');
        $stmt->execute(['id' => $task_id]);
        $stmt->fetch();
        $stmt = $pdo->prepare('DELETE FROM task_assignments WHERE task_id = :task_id');
        $stmt->execute(['task_id' => $task_id]);
        $stmt->fetch();
        header('Location: /' . $task_id);
      } else {
        echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
        echo '<header class="alert-bar">メンバーが見つかりません。</header>';
      }
    }
  }
