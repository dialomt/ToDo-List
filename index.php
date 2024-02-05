<?php

require 'db_conn.php';



if (isset($_POST['title']) && isset($_POST['description'])) {

    $title = $_POST['title'];

    $description = $_POST['description'];
    $stmt = $conn->prepare("INSERT INTO todos(title, description, date_time) VALUES(?, ?, NOW())");

    $stmt->execute([$title, $description]);
    header("Location: index.php");
}

$todos = $conn->query("SELECT * FROM todos ORDER BY id DESC");



if (isset($_POST['edit_id']) && isset($_POST['new_title']) && isset($_POST['new_description'])) {
    $id = $_POST['edit_id'];
    $new_title = $_POST['new_title'];
    $new_description = $_POST['new_description'];
    $stmt = $conn->prepare("UPDATE todos SET title = ?, description = ? WHERE id = ?");
    $stmt->execute([$new_title, $new_description, $id]);
    header("Location: index.php");
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM todos WHERE id=?");
    $stmt->execute([$id]);
    header("Location: index.php");
}


if (isset($_GET['complete_id'])) {
    $id = $_GET['complete_id'];
    $stmt = $conn->prepare("SELECT checked FROM todos WHERE id = ?");
    $stmt->execute([$id]);
    $checked = $stmt->fetchColumn();
    $new_checked = $checked ? 0 : 1;
    $stmt = $conn->prepare("UPDATE todos SET checked = ? WHERE id = ?");
    $stmt->execute([$new_checked, $id]);
    header("Location: index.php");
}


if (isset($_GET['sort_by'])) {
    $sort_by = $_GET['sort_by'];
    $todos = $conn->query("SELECT * FROM todos ORDER BY " . $sort_by);
}


$edit_id = isset($_GET['edit_id']) ? $_GET['edit_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="main-section">
        <div class="add-section">
            <form action="index.php" method="POST">
                <input type="text" name="title" placeholder="Título de la tarea" required>
                <textarea name="description" placeholder="Descripción de la tarea" required></textarea>
                <input type="submit" value="Crear tarea">
            </form>
        </div>
        <?php


        $todos = $conn->query("SELECT * FROM todos ORDER BY id DESC");
        ?>
        <div class="show-todo-section">

            <?php while ($todo = $todos->fetch(PDO::FETCH_ASSOC)) { ?>
                <div class="todo-item <?php echo $todo['checked'] ? 'completed' : '' ?>">
                    <div class="task-buttons">

                        <a href="index.php?complete_id=<?php echo $todo['id']; ?>" class="complete-to-do"><?php echo $todo['checked'] ? '☑' : '☐'; ?></a>

                        <a href="index.php?edit_id=<?php echo $todo['id']; ?>" class="edit-to-do">M</a>
                        <a href="index.php?delete_id=<?php echo $todo['id']; ?>" class="remove-to-do">X</a>
                    </div>

                    <h2 class="<?php echo $todo['checked'] ? 'completed-task' : ''; ?>"><?php echo $todo['title']; ?></h2>
                    <p><?php echo $todo['description']; ?></p>
                    <p><small>Fecha de creación: <?php echo date('d-m-Y', strtotime($todo['date_time'])); ?></small></p>


                    <?php if ($edit_id == $todo['id']) { ?>
                        <form action="index.php" method="POST">
                            <input type="hidden" name="edit_id" value="<?php echo $todo['id']; ?>">
                            <input type="text" name="new_title" value="<?php echo $todo['title']; ?>" required>
                            <textarea name="new_description" required><?php echo $todo['description']; ?></textarea>
                            <button type="submit" class="edit-to-do">Guardar</button>
                        </form>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</body>

</html>