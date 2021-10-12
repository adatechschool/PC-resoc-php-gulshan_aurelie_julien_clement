<?php
session_start(); 
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mes abonnés </title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <?php
            $mysqli = new mysqli("localhost:3306", "root", "root", "socialnetwork");
            $mysqli->set_charset("utf8mb4");

            $userEnSql = "SELECT users.id, posts_tags.tag_id FROM `users`"
            ." INNER JOIN `posts` ON posts.user_id = users.id"
            ." INNER JOIN `posts_tags` ON posts_tags.post_id = posts.id";

            $userInfo = $mysqli->query($userEnSql);

            if ($link = $userInfo->fetch_assoc())
            {   
                $tagId = $link['tag_id'];
                $userId = $link['id'];
            } ?>
        <header>
            <img src="resoc.jpg" alt="Logo de notre réseau social"/> 

            <?php
                include("menu.php");
                print_menu(isset($_SESSION['connected_id']) ? $_SESSION['connected_id'] : 0);
            ?> 
        </header>

        <div id="wrapper">          
            <aside>
                <img src = "user.jpg" alt = "Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez la liste des personnes qui
                        suivent les messages de l'utilisatrice
                        n° <?php echo $_GET['user_id'] ?></p>
                </section>
            </aside>
            <main class='contacts'>
                <?php
                // Etape 1: récupérer l'id de l'utilisateur
                $userId = $_GET['user_id'];

                // Etape 3: récupérer le nom de l'utilisateur
                $laQuestionEnSql = "SELECT `users`.* "
                        . "FROM `followers` "
                        . "LEFT JOIN `users` ON `users`.`id`=`followers`.`following_user_id` "
                        . "WHERE `followers`.`followed_user_id`='" . intval($userId) . "'"
                        . "GROUP BY `users`.`id`"
                ;
                $lesInformations = $mysqli->query($laQuestionEnSql);
                while ($user = $lesInformations->fetch_assoc())
                {
                // Etape 4: à vous de jouer
                //@todo: faire la boucle while de parcours des abonnés et mettre les bonnes valeurs ci dessous 
                ?>
                <article>
                    <img src="user.jpg" alt="blason"/>
                    <p><a href="wall.php?user_id=<?php echo $user['id']?>"><?php echo $user['alias'] ?></p>
                    <p>id: <?php echo $user['id'] ?></p>                    
                </article>
                <?php } ?>
            </main>
        </div>
    </body>
</html>
