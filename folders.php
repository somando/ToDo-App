<?php
  session_start();
  
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
    if (!isset($_SESSION['user_id'])) {
      echo '<header class="alert-bar">ToDo Listを利用するにはログインが必要です。<a href="/signin.php">ログイン</a> <a href="/signup.php">新規登録</a></header>';
    } else {
      echo '<header class="welcome">ようこそ、'. $_SESSION['user_id'] . 'さん。 <a href="/signout.php">ログアウト</a></header>';
      $pdo = new PDO('mysql:host=db;dbname=todo', 'root', 'root');
    }
  }
?>

<meta>
  <title>フォルダ詳細｜ToDo List</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/style.css">
</meta>

<main>

<h1>Folder</h1>

<?php
  if (isset($pdo)) {
    echo '  <div class="folder-container">';
    $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE folder_id = :id AND user_id = :user_id');
    $stmt->execute(['id' => $_GET['id'], 'user_id' => $_SESSION['user_id']]);
    if ($stmt->fetch()) {
      $stmt = $pdo->prepare('SELECT * FROM folders WHERE id = :id');
      $stmt->execute(['id' => $_GET['id']]);
      $folder = $stmt->fetch();
      if ($folder) {
        echo '    <div class="content-block">';
        echo '      <h3 class="folder-title">' . $folder['name'] . '</h3>';
        echo '      <p>' . $folder['description'] . '</p>';
        echo '      <div class="item-menu">';
        echo '        <p><a href="/tasks/new.php?folder_id=' . $folder['id'] . '" class="item-add add-item">タスクを追加する</a></p>';
        echo '        <p><a href="/folders/members.php?id=' . $folder['id'] . '" class="item-member">メンバー</a></p>';
        echo '        <p><a href="/folders/edit.php?id=' . $folder['id'] . '" class="item-edit">編集</a></p>';
        echo '        <p><a href="/folders/delete.php?id=' . $folder['id'] . '" class="item-delete">削除</a></p>';
        echo '      </div>';
        echo '      <div class="folder-task">';

        $stmt = $pdo->prepare('SELECT * FROM tasks WHERE folder_id = :folder_id AND done = false');
        $stmt->execute(['folder_id' => $folder['id']]);
        $tasks = $stmt->fetchAll();

        if ($tasks) {
          foreach ($tasks as $task) {
            echo '          <p class="folder-task-item folder-task-item-not-done"><a href="/tasks.php?id=' . $task['id'] . '">' . $task['title'] . '</a></p>';
          }
        } else {
          echo '          <p>未完了のタスクはありません。</p>';
        }
        
        echo '      </div>';
        echo '      <div class="folder-task">';
        echo '        <h4 class="folder-sub-title">完了済みのタスク</h4>';
        $stmt = $pdo->prepare('SELECT * FROM tasks WHERE folder_id = :folder_id AND done = true');
        $stmt->execute(['folder_id' => $folder['id']]);
        $tasks = $stmt->fetchAll();

        if ($tasks) {
          foreach ($tasks as $task) {
            echo '          <p class="folder-task-item folder-task-item-done"><a href="/tasks.php?id=' . $task['id'] . '">' . $task['title'] . '</a></p>';
          }
        } else {
          echo '          <p>完了済みのタスクはありません。</p>';
        }
        echo '      </div>';
        echo '    </div>';
      } else {
        echo '<header class="alert-bar">フォルダが見つかりません。</header>';
      }
    } else {
      echo '<header class="alert-bar">フォルダが見つかりません。</header>';
    }
    echo '  </div>';
  }
