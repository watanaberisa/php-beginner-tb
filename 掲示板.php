<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>掲示板</title>
</head>
<body>
    <?php
        //DB接続設定
        $dsn = 'データベース名';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //テーブルの作成(table5がテーブル名)
        $sql = "CREATE TABLE IF NOT EXISTS table5"
        ."("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date DATETIME,"
        . "pass TEXT"
        .");";
        $stmt = $pdo->query($sql);
    //編集対象番号が送信された時の処理
        if(isset($_POST["edit"])){
            $id = $_POST["edit"];
            $sql = 'SELECT * FROM table5 WHERE id=:id'; //送信された番号(id)のデータを取り出す
            $stmt = $pdo->prepare($sql);                
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll();
            foreach($results as $row){
                if($row['id'] == $id){  //編集番号と投稿番号が一致した時
                    if(isset($_POST["edit_pass"]) && $row['pass'] == $_POST["edit_pass"]){  //パスワードが一致した時
                        $e_name = $row['name'];
                        $e_com = $row['comment'];
                        $e_pass = $row['pass'];
                    }else{  //パスワードが間違っていた場合
                        echo "パスワードの入力にミスがあります";
                    }
                }
            }
        }
    //編集内容の書き込み
        if(isset($_POST["editnum"]) && $_POST["editnum"] >0){ 
            if(isset($_POST["name"]) && !empty($_POST["name"]) && isset($_POST["comment"])&& !empty($_POST["comment"]) && !empty($_POST["pass"])){
                $id = $_POST["editnum"];    //編集する投稿番号
                $name = $_POST["name"];     //編集したい名前
                $comment = $_POST["comment"]; //編集したいコメント
                $date = date("YmdHis"); //dateのY,m,dなどは全部つめて書くと表示の時に自動で区切れる
                $pass = $_POST["pass"];  
    //UPDATE文による編集
                $sql = 'UPDATE table5 SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);                
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_INT);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->execute();
            }
        }else{
    //新規投稿の書き込み
            if(isset($_POST["name"]) && !empty($_POST["name"]) && isset($_POST["comment"]) && !empty($_POST["comment"]) && !empty($_POST["pass"])){
    //INSERT文による書き込み
                $sql = $pdo-> prepare("INSERT INTO table5 (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
                $sql->bindParam(':name', $name, PDO::PARAM_STR);
                $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql->bindParam(':date', $date, PDO::PARAM_INT);
                $sql->bindParam(':pass', $pass, PDO::PARAM_STR); 
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $date = date("YmdHis");
                $pass = $_POST["pass"]; 
                $sql -> execute();
            }elseif(isset($_POST["delete"]) && isset($_POST["del_pass"])){
    //削除対象番号が送信された時の処理   
                $id = $_POST["delete"];
                $sql = 'SELECT * FROM table5 WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll();
                foreach($results as $row){
                    if($row['id'] == $id){
                        if($row['pass'] == $_POST["del_pass"]){
    //idもpassも合致した時、DELETE文で削除する
                            $sql = 'delete from table5 where id=:id';
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                            $stmt->execute();
                        }if($row['pass'] != $_POST["del_pass"]){
                            echo "パスワードの入力にミスがあります";
                        }
                    }
                }
            }
        }
    ?>
<!--投稿フォーム-->
<form action=""  method="post" > 
    <input type="hidden" name="editnum" placeholder="編集番号" value=<?php if(isset($_POST["edit"])){echo $id;}?>> 
    <input type="text" name="name" value=<?php if(isset($e_name)){echo $e_name;}else{echo "名前";}?>> 
    <input type="text" name="comment" value=<?php if(isset($e_com)){echo $e_com;}else{echo "コメント";}?>>
    <input type="text" name="pass" value=<?php if(isset($e_pass)){echo $e_pass;}else{echo "パスワード";}?>>
    <input type="submit" name="submit"> 
    </form> 
<!--削除フォーム--> 
<form action=""  method="post" > 
    <input type="number" name="delete" placeholder="削除対象番号">
    <input type="text" name="del_pass" placeholder="パスワード">
    <input type="submit" name="submit" value="削除"> 
</form> 
<!--編集フォーム-->
<form action=""  method="post" > 
    <input type="number" name="edit" placeholder="編集対象番号">
    <input type="text" name="edit_pass" placeholder="パスワード">
    <input type="submit" name="submit" value="編集"> 
</form> 
<?php
    //SELECT文を用いてテーブル内の値を表示する
    $sql = 'SELECT * FROM table5';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach($results as $row){
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date'].'<br>';
    echo "<hr>";
    }
?>
</body>
</html>