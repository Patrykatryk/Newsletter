<?php

session_start();

if (isset($_POST['email']))
{
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if(empty($email)) {

        $_SESSION['given_email']= $_POST['email'];
        header('Location: index.php');

    } 
    else {

        $checkem=$_POST['email'];
        require_once 'database.php';
        $check = $db -> query('SELECT email FROM users');
        $licznik=0;
        $id=1;
        while($row = $check->fetch()) 
        {  
            $id +=1; 
            if($checkem==$row['email'])
            {
                $licznik+=1;
            }
        }
        if (($licznik>0) && ($_SESSION['delete']==true))
        {
            $delete = $db -> prepare("DELETE FROM users WHERE email LIKE :email");
            $delete -> bindValue(':email', $checkem, PDO::PARAM_STR);
            $delete -> execute();
            $_SESSION['delete']=false;
        }
        elseif (($licznik==0) && ($_SESSION['delete']==false) && ($_SESSION['save']==true))
        {
            $query = $db -> prepare('INSERT INTO users VALUES (:id, :email)');
            $query -> bindValue(':id', $id, PDO::PARAM_STR);
            $query -> bindValue(':email', $email, PDO::PARAM_STR);
            $query -> execute();
            $_SESSION['save']=false;
        } elseif ($_SESSION['save']==true) {
            $_SESSION['same_email']='Podany adres jest już zapisany na listę!';
            header('Location: index.php');
            exit();
        } else {
            header('Location: index.php');
            exit();
        }
        
    }
} else {

    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>

    <meta charset="utf-8">
    <title>Zapisanie się do newslettera</title>
    <meta name="description" content="Używanie PDO - zapis do bazy MySQL">
    <meta name="keywords" content="php, kurs, PDO, połączenie, MySQL">

    <meta http-equiv="X-Ua-Compatible" content="IE=edge">

    <link rel="stylesheet" href="main.css">
    <link href="https://fonts.googleapis.com/css?family=Lobster|Open+Sans:400,700&amp;subset=latin-ext" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <![endif]-->

</head>

<body>

    <div class="container">

        <header>
            <h1>Newsletter</h1>
        </header>

        <main>
            <?php
            if ($licznik==0)
            {
                echo "<p>Dziękujemy za zapisanie się na listę mailową naszego newslettera!</p>";
            } else {
                echo "<p>Szkoda, że wypisałes się  z newslettera. Dziękujemy, że byłeś z nami</p>";
            }
                
            // <article>
            //     <p>Dziękujemy za zapisanie się na listę mailową naszego newslettera!</p>
            // </article>
            ?>
        </main>

    </div>

</body>
</html>