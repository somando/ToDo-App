<?php
  session_start();
  echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
  if (!isset($_SESSION['user_id'])) {
    echo '<header class="alert-bar">ToDo Listを利用するにはログインが必要です。<a href="/signin.php">ログイン</a> <a href="/signup.php">新規登録</a></header>';
  } else {
    echo '<header class="welcome">ようこそ、'. $_SESSION['user_id'] . 'さん。 <a href="/signout.php">ログアウト</a></header>';
    $pdo = new PDO('mysql:host=db;dbname=todo', 'root', 'root');
  }
?>

<meta>
  <title>ToDo List</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/style.css">
</meta>

<main>

<?php

  if (isset($pdo)) {

    echo '<div class="main-container">';
    echo '  <div class="grid-container">';
    echo '    <div class="container tasks">';
    echo '      <h2>Tasks</h2>';
    echo '      <div class="content-block">';

    $stmt = $pdo->prepare('SELECT folders.name, folders.id FROM folders_users JOIN folders ON folders_users.folder_id = folders.id WHERE folders_users.user_id = :user_id');
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $folders = $stmt->fetchAll();

    if ($folders) {
      foreach ($folders as $folder) {
        echo '        <div class="tasks-folder">';
        echo '        <p class="tasks-folder-name">' . $folder['name'] . '</p>';

        $stmt = $pdo->prepare('SELECT * FROM tasks WHERE folder_id = :folder_id AND done = false');
        $stmt->execute(['folder_id' => $folder['id']]);
        $tasks = $stmt->fetchAll();

        if ($tasks) {
          foreach ($tasks as $task) {
            echo '        <p class="task-item"><a href="/tasks.php?id=' . $task['id'] . '">' . $task['title'] . '</a></p>';
          }
        }
        echo '        <a href="/tasks/new.php?folder_id=' . $folder['id'] . '" class="add-item">タスクを追加する</a>';
        echo '        </div>';
      }
      echo '        <p>すでに完了済みのタスクはフォルダから閲覧できます。</p>';
    } else {
      echo '        <p>フォルダーが存在しません。タスクを追加するにはフォルダーが必要です。</p>';
    }

    echo '      </div>';
    echo '    </div>';
    echo '  </div>';

    echo '  <div class="grid-container">';
    echo '    <div class="container folders">';
    echo '      <h2>Folders</h2>';
    echo '      <div class="content-block">';

    if ($folders) {
      foreach ($folders as $folder) {
        echo '        <p class="folder-item"><a href="/folders.php?id=' . $folder['id'] . '">' . $folder['name'] . '</a></p>';
      }
    }

    echo '        <a href="/folders/new.php" class="add-item">フォルダを追加する</a>';
    echo '      </div>';
    echo '    </div>';

    echo '  </div>';
    echo '</div>';
  }
?>

</main>
