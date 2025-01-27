<?php
  session_start();
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = new PDO('mysql:host=db;dbname=todo', 'root', 'root');
    $stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = :id');
    $stmt->execute(['id' => $_POST['id']]);
    $task = $stmt->fetch();
    $task_folder_id = $task['folder_id'];
    $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE user_id = :user_id AND folder_id = :folder_id');
    $stmt->execute(['user_id' => $_SESSION['user_id'], 'folder_id' => $task_folder_id]);
    if ($stmt->fetch()) {
      $stmt = $pdo->prepare('UPDATE tasks SET done = :done WHERE id = :id');
      $stmt->execute(['done' => ($_POST['done'] === "true" ? 1 : 0), 'id' => $_POST['id']]);
      header('Location: /tasks.php?id=' . $_POST['id']);
    } else {
      echo '<h1 class="app-title"><a href="/">ToDo List</a></h1>';
      echo '<header class="alert-bar">このタスクにアクセスする権限がありません。</header>';
    }
  } else {
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
  <title>タスク詳細｜ToDo List</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/style.css">
</meta>

<main>

<h1>Task</h1>

<?php
  if (isset($pdo)) {
    echo '  <div class="task-container">';
    $stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = :id');
    $stmt->execute(['id' => $_GET['id']]);
    $task = $stmt->fetch();
    if ($task) {
      $task_folder_id = $task['folder_id'];
      $stmt = $pdo->prepare('SELECT * FROM folders_users WHERE user_id = :user_id AND folder_id = :folder_id');
      $stmt->execute(['user_id' => $_SESSION['user_id'], 'folder_id' => $task_folder_id]);
      if ($stmt->fetch()) {
        echo '    <div class="content-block">';
        echo '      <h3 class="task-title">' . $task['title'] . '</h3>';
        if ($task['done']) {
          echo '        <p class="task-status task-finish">完了済み</p>';
        } else {
          echo '        <p class="task-status task-no-finish">未完了</p>';
        }
        echo '      <p>' . $task['description'] . '</p>';
        echo '      <form method="post" name="status-form" class="link-form">';
        echo '        <input type="hidden" name="id" value="' . $task['id'] . '">';
        if ($task['done']) {
          echo '        <input type="hidden" name="done" value="false">';
          echo '        <a href="javascript:document.forms[\'status-form\'].submit()" class="task-status-submit task-status-submit-not-completed">未完了する</a>';
        } else {
          echo '        <input type="hidden" name="done" value="true">';
          echo '        <a href="javascript:document.forms[\'status-form\'].submit()" class="task-status-submit task-status-submit-completed">完了する</a>';
        }
        echo '      </form>';
        echo '      <a href="/tasks/edit.php?id=' . $task['id'] . '" class="item-edit">編集</a>';
        echo '      <form method="post" action="/tasks/delete.php" name="delete-item">';
        echo '        <input type="hidden" name="id" value="' . $task['id'] . '">';
        echo '        <a href="javascript:void(0)" class="item-delete" onclick="submitForm(\'delete-item\')">削除</a>';
        echo '      </form>';
        echo '    </div>';
      } else {
        echo '<header class="alert-bar">タスクが見つかりません。</header>';
      }
    } else {
      echo '<header class="alert-bar">タスクが見つかりません。</header>';
    }
    echo '  </div>';
  }
?>

<script>
  function submitForm(formName) {
    var form = document.forms[formName];
    if (form) {
      form.submit();
    } else {
      console.error('フォームが見つかりません: ' + formName);
    }
  }
</script>
