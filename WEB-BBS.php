<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>簡易掲示板の作成</title>
    <style>
        body {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            font-family: sans-serif;
        }
        input[type="text"], 
        input[type="number"],
        input[type="password"] {
            padding: 5px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        input[type="submit"] {
            padding: 5px 15px;
            margin: 5px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .comment-box {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }
        .form-section {
            background: #f5f5f5;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>  
            <?php
            
            // DB接続設定
            $dsn = 'mysql:dbname=YOUR_DB_NAME;host=localhost';
            $user = 'YOUR_DB_USER';
            $password = 'YOUR_DB_PASSWORD';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            // $pdo->exec("SET time_zone = '+09:00'");

            if (isset($_POST["post_comment"])) {
                if(isset($_POST["username"]) && $_POST["username"] !== ""){
                    if(isset($_POST["comment"]) && $_POST["comment"] !== ""){
                        if(isset($_POST["id"])  && $_POST["id"] !== ""){
                            $id = $_POST["id"];
                            $username = $_POST["username"];
                            $comment = $_POST["comment"];
                            $password = $_POST["password"];
                            $submission_time = date('Y-m-d H:i:s');
                            $sql = 'UPDATE m5data SET username=:username, comment=:comment, time=:time, password=:password WHERE id=:id';
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                            $stmt->bindParam(':password', $password, PDO::PARAM_STR); 
                            $stmt->bindParam(':time', $submission_time, PDO::PARAM_STR);
                            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                            $stmt->execute();
                            echo "<br><br>コメント編集を受け付けました<br><br>";
                        }else{
                            $submission_time = date('Y-m-d H:i:s');
                            $username = $_POST["username"];
                            $comment = $_POST["comment"];
                            $password = $_POST["password"];
                            
                            $sql = "INSERT INTO m5data (username, comment, password) VALUES (:username, :comment, :password)";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR); 
                            $stmt->bindParam(':password', $password, PDO::PARAM_STR); 
                            //実行
                            $stmt->execute();
                            echo "<br><br>コメント入力を受け付けました<br><br>";
                        }
                    }else{
                        echo "<br><br>コメントが空です<br><br>";
                    }
                }else{
                    echo "<br><br>名前が空です<br><br>";
                }
            }

            $edit_id ='';
            $edit_username = '';
            $edit_comment = '';
            $edit_password='';
            if(isset($_POST["edit_comment"])){
                if(isset($_POST["edit_id"]) && $_POST["edit_id"] !== ""){
                    if(isset($_POST["edit_password"]) && $_POST["edit_password"] !== ""){
                        $id = $_POST["edit_id"];
                        $sql = "SELECT * FROM m5data WHERE id = :id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                        $row = $stmt->fetch();
                            if($row){
                                $edit_username = $row['username'];
                                $edit_comment = $row['comment'];
                                $edit_password = $row['password'];
                                $edit_id = $row['id']; 
                            }
                            if($_POST["edit_password"]==$edit_password){
                                echo "<br><br>パスワード認証成功<br><br>";
                            }else{
                                echo "<br><br>パスワードが違います<br><br>";
                                $edit_username = '';
                                $edit_comment = '';
                                $edit_password = '';
                                $edit_id ='';
                            }
                    }else{
                        echo "<br><br>パスワードが入力されていません<br><br>";
                    }
                }
            }

            $delete_password='';
            $delete_id='';
            if (isset($_POST["delete_comment"])) {
                if(isset($_POST["delete_id"]) && $_POST["delete_id"] !== ""){
                    if(isset($_POST["delete_password"]) && $_POST["delete_password"] !== ""){
                        $id = $_POST["delete_id"];
                        $sql = "SELECT * FROM m5data WHERE id = :id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                        $row = $stmt->fetch();
                            if($row){
                                $delete_password = $row['password'];
                                $delete_id = $row['id']; 
                            }
                            if($_POST["delete_password"]==$delete_password){
                                $sql = 'delete from m5data where id=:id';
                                $stmt = $pdo->prepare($sql);
                                $stmt->bindValue(':id', $id, PDO::PARAM_INT); 
                                //実行
                                $stmt->execute();
                                echo "<br><br>コメントを削除しました<br><br>";
                            }else{
                                echo "<br><br>パスワードが違います<br><br>";
                                $delete_password='';
                                $delete_id='';
                            }
                    }else{
                        echo "<br><br>パスワードが入力されていません<br><br>";
                    }
                }else{
                    echo "<br><br>削除したいコメントid,パスワードを入力してください<br><br>";
                }
            }

            echo "<hr>";

            $sql = 'SELECT * FROM m5data';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll(); 
            if (empty($results)) {
                echo "まだ投稿がありません。<br>";
            } else {
                foreach ($results as $row){
                    echo $row['id'].',';
                    echo $row['username'].',';
                    echo $row['comment'].',';
                    echo $row['time'].'<br>';
                    echo "<hr>";
                }
            }
            
        ?>    
        <div class="form-section">
            <h3>コメント入力フォーム</h3>
            <form action="" method="post">
                <input type="text" name="username" placeholder="名前" value="<?php echo $edit_username; ?>">
                <input type="text" name="comment" placeholder="コメント" value="<?php echo $edit_comment; ?>">
                <input type="hidden" name="id" value="<?php echo $edit_id; ?>" readonly>
                <input type="text" name="password" placeholder="パスワード">
                <input type="submit" name="post_comment">
            </form>
        </div>

        <div class="form-section">
            <h3>コメント編集、番号指定用フォーム</h3>
            <form action="" method="post">
                <input type="number" name="edit_id" placeholder="id(投稿番号)">
                <input type="text" name="edit_password" placeholder="パスワード">
                <input type="submit" name="edit_comment" value="編集">
            </form>
        </div>

        <div class="form-section">
            <h3>コメント削除フォーム</h3>
            <form action="" method="post">
                <input type="number" name="delete_id" placeholder="id(投稿番号)">
                <input type="text" name="delete_password" placeholder="パスワード">
                <input type="submit" name="delete_comment" value="削除">
            </form>
        </div>
</body>
</html>