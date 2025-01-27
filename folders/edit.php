<?php

  session_start();
  
  $pdo = new PDO('mysql:host=db;dbname=todo', 'root', 'root');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['title'];
    $description = $_POST['description'];

    if ($name === '') {
      echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
      echo '<header class="alert-bar">必須事項が入力されていません。</header>';
    } else {
      $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE folder_id = :folder_id AND user_id = :user_id');
      $stmt->execute(['folder_id' => $id, 'user_id' => $_SESSION['user_id']]);
      if (!$stmt->fetch()) {
        echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
        echo '<header class="alert-bar">フォルダが見つかりません。</header>';
      } else {
        $stmt = $pdo->prepare('UPDATE folders SET name = :name, description = :description WHERE id = :id');
        $stmt->execute(['name' => $name, 'description' => $description, 'id' => $_POST['id']]);
        $folder = $stmt->fetch();
        header('Location: /folders.php?id=' . $id);
      }
    }
  } else {
    echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
    if (!isset($_SESSION['user_id'])) {
      echo '<header class="alert-bar">ToDo Listを利用するにはログインが必要です。<a href="/signin.php">ログイン</a> <a href="/signup.php">新規登録</a></header>';
    } else {
      echo '<header class="welcome">ようこそ、'. $_SESSION['user_id'] . 'さん。 <a href="/signout.php">ログアウト</a></header>';
    }
    $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE user_id = :user_id AND folder_id = :folder_id');
    $stmt->execute(['user_id' => $_SESSION['user_id'], 'folder_id' => $_GET['id']]);
    if (!($stmt->fetch())) {
      echo '<header class="alert-bar">フォルダが見つかりません。</header>';
    }
    $stmt = $pdo->prepare('SELECT * FROM folders WHERE id = :id');
    $stmt->execute(['id' => $_GET['id']]);
    $folder = $stmt->fetch();
  }
?>

<meta>
  <title>フォルダ編集｜ToDo List</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/style.css">
</meta>

<main>
  <h1>Edit Folder</h1>
  <form method="post" class="account-form">
    <?php
      echo '<input type="hidden" name="id" value="' . $folder['id'] . '">';
    ?>
    <label for="name" class="form-label-folder">フォルダ名:</label>
    <?php
      echo '<input type="text" name="title" id="input-text" value="' . $folder['name'] . '" required>';
    ?>
    <br>
    <label for="description" class="form-label-description">説明:</label>
    <?php
      echo '<textarea name="description" id="input-text">' . $folder['description'] . '</textarea>';
    ?>
    <div class="center">
      <button type="submit">編集</button>
    </div>
  </form>
</main>
