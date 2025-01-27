<?php

  session_start();
  
  $pdo = new PDO('mysql:host=db;dbname=todo', 'root', 'root');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $folder_id = $_POST['folder_id'];
    $user_id = $_SESSION['user_id'];
    if (isset($_POST['assigns'])) {
      $assigns = $_POST['assigns'];
    }

    if ($title === '' || $folder_id === '') {
      echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
      echo '<header class="alert-bar">必須事項が入力されていません。</header>';
    } else {
      $stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = :id');
      $stmt->execute(['id' => $_POST['id']]);
      $task = $stmt->fetch();
      if (!$task) {
        echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
        echo '<header class="alert-bar">タスクが見つかりません。</header>';
      } else {
        $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE folder_id = :folder_id AND user_id = :user_id');
        $stmt->execute(['folder_id' => $task['folder_id'], 'user_id' => $user_id]);
        if ($stmt->fetch() === false) {
          echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
          echo '<header class="alert-bar">このフォルダにアクセスする権限がありません。</header>';
        } else {
          $stmt = $pdo->prepare('UPDATE tasks SET title = :title, description = :description, folder_id = :folder_id WHERE id = :id');
          $stmt->execute(['title' => $title, 'description' => $description, 'folder_id' => $folder_id, 'id' => $_POST['id']]);
          $task = $stmt->fetch();
          if (isset($assigns)) {
            $stmt = $pdo->prepare('DELETE FROM task_assignments WHERE task_id = :task_id');
            $stmt->execute(['task_id' => $_POST['id']]);
            $task = $stmt->fetch();
            foreach ($assigns as $assign) {
              $stmt = $pdo->prepare('INSERT INTO task_assignments (task_id, user_id) VALUES (:task_id, :user_id)');
              $stmt->execute(['task_id' => $id, 'user_id' => $assign]);
              $task_user = $stmt->fetch();
            }
          }
          header('Location: /tasks.php?id=' . $id);
        }
      }
    }
  } else {
    echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
    if (!isset($_SESSION['user_id'])) {
      echo '<header class="alert-bar">ToDo Listを利用するにはログインが必要です。<a href="/signin.php">ログイン</a> <a href="/signup.php">新規登録</a></header>';
    } else {
      echo '<header class="welcome">ようこそ、'. $_SESSION['user_id'] . 'さん。 <a href="/signout.php">ログアウト</a></header>';
    }
    $stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = :id');
    $stmt->execute(['id' => $_GET['id']]);
    $task = $stmt->fetch();
    $task_folder_id = $task['folder_id'];
    $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE user_id = :user_id AND folder_id = :folder_id');
    $stmt->execute(['user_id' => $_SESSION['user_id'], 'folder_id' => $task_folder_id]);
    if (!($stmt->fetch())) {
      echo '<header class="alert-bar">このタスクにアクセスする権限がありません。</header>';
    }
    $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE folder_id = :folder_id');
    $stmt->execute(['folder_id' => $task_folder_id]);
    $users = $stmt->fetchAll();
    $stmt = $pdo->prepare('SELECT * FROM task_assignments WHERE task_id = :task_id');
    $stmt->execute(['task_id' => $task['id']]);
    $assigned_users = $stmt->fetchAll();
    $assigned_user_ids = [];
    foreach ($assigned_users as $assigned_user) {
      $assigned_user_ids[] = $assigned_user['user_id'];
    }
  }
?>

<meta>
  <title>タスク編集｜ToDo List</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/style.css">
</meta>

<main>
  <h1>Edit Task</h1>
  <form method="post" class="account-form">
    <?php
      echo '<input type="hidden" name="id" value="' . $task['id'] . '">';
    ?>
    <label for="name" class="form-label-task">タスク名:</label>
    <?php
      echo '<input type="text" name="title" id="input-text" value="' . $task['title'] . '" required>';
    ?>
    <br>
    <label for="description" class="form-label-description">説明:</label>
    <?php
      echo '<textarea name="description" id="input-text">' . $task['description'] . '</textarea>';
    ?>
    <br>
    <label for="folder_id" class="form-label-folder">フォルダ:</label>
    <select name="folder_id" id="folder-id" onchange="changeFolder();" required>
      <?php
        $stmt = $pdo->prepare('SELECT folders.name, folders.id FROM folders_users JOIN folders ON folders_users.folder_id = folders.id WHERE folders_users.user_id = :user_id');
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $folders = $stmt->fetchAll();
        if ($folders) {
          foreach ($folders as $folder) {
            echo '<option value="' . $folder['id'] . '" ' . (($folder['id'] === $task['folder_id']) ? 'selected' : '') . '>' . $folder['name'] . '</option>';
          }
        } else {
          echo '<option value="" disabled>-- フォルダがありません --</option>';
        }
      ?>
    </select>
    <br>
    <label for="assign" class="form-label-users">アサイン:</label>
    <div id="assign-list">
      <?php
        foreach ($users as $user) {
          echo '<p><input type="checkbox" name="assigns[]" value="' . $user['user_id'] . '" id="' . $user['user_id'] . '" ' . (in_array($user['user_id'], $assigned_user_ids) ? 'checked' : '') . '><label for="' . $user['user_id'] . '" class="assign-userid">' . $user['user_id'] . (($user['user_id'] === $_SESSION['user_id']) ? '（あなた）' : '') . '</label></p>';
        }
      ?>
    </div>
    <div class="center">
      <button type="submit">編集</button>
    </div>
  </form>
</main>

<script>
  function changeFolder() {
    var assign_list = document.getElementById('assign-list');
    assign_list.innerHTML = 'Loading...';
    var folder_id = document.getElementById('folder-id').value;
    var request = new XMLHttpRequest();
    request.open('GET', '/folders/users.php?folder_id=' + String(folder_id), true);
    request.responseType = 'json';
    request.onload = function() {
      var users = request.response;
      console.log(users);
      if (users.length === 0) {
        assignList.innerHTML = '<p>フォルダが見つかりません。</p>';
      } else {
        assign_list.innerHTML = '';
        users.forEach(function(user) {
          assign_list.innerHTML += '<p><input type="checkbox" name="assigns[]" value="' + user['user_id'] + '" id="' + user['user_id'] + '"><label for="' + user['user_id'] + '" class="assign-userid">' + user['user_id'] + (user['is_self'] && '（あなた）') + '</label></p>';
        });
        assign_list.innerHTML += '<p>フォルダを変更した場合、アサインをし直す必要があります。</p>'
      }
    };
    request.send();
  }
</script>
