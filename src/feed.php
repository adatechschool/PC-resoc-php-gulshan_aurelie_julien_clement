<?php
session_start();
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Flux</title>         
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
            <img src="resoc.jpg" alt="Logo de notre réseau social"/>
            <?php
                include("menu.php");
                print_menu(isset($_SESSION['connected_id']) ? $_SESSION['connected_id'] : 0);
            ?> 
        </header>
        <div id="wrapper">
            <?php
            $mysqli = new mysqli("localhost:3306", "root", "root", "socialnetwork");
            $mysqli->set_charset("utf8mb4");

            if (isset($_GET['user_id']))
            {
                $userId=$_GET['user_id'];
            }
            else {
                $userId=$_SESSION['connected_id'];
            }
            ?>

            <aside>
                <?php
                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 */
                $laQuestionEnSql = "SELECT * FROM `users` WHERE id=" . intval($userId);
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                ?>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les message des utilisatrices
                        auxquel est abonnée l'utilisatrice <?php echo $user['alias'] ?>
                        (n° <?php echo $userId ?>)
                    </p>

                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 3: récupérer tous les messages des abonnements
                 */
                $laQuestionEnSql = "SELECT `posts`.`content`,"
                        . "`posts`.`created`,"
                        . "`users`.`alias` as author_name,  "
                        . "count(DISTINCT`likes`.`id`) as like_number,  "
                        . "GROUP_CONCAT(DISTINCT `tags`.`label`) AS taglist "
                        . "FROM `followers` "
                        . "JOIN `users` ON `users`.`id`=`followers`.`followed_user_id`"
                        . "JOIN `posts` ON `posts`.`user_id`=`users`.`id`"
                        . "LEFT JOIN `posts_tags` ON `posts`.`id` = `posts_tags`.`post_id`  "
                        . "LEFT JOIN `tags`       ON `posts_tags`.`tag_id`  = `tags`.`id` "
                        . "LEFT JOIN `likes`      ON `likes`.`post_id`  = `posts`.`id` "
                        . "WHERE `followers`.`following_user_id`='" . intval($userId) . "' "
                        . "GROUP BY `posts`.`id`"
                        . "ORDER BY `posts`.`created` DESC  ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }
                while ($post = $lesInformations->fetch_assoc())
                {
                ?>
                <article>
                    <h3>
                        <time datetime='2020-02-01 11:12:13' >31 février 2010 à 11h12</time>
                    </h3>
                    <address>par <?php echo $post['author_name'] ?></address>
                    <div>
                        <p><?php echo $post['content'] ?></p>
                    </div>                                            
                    <footer>
                        <small>♥ <?php echo $post['like_number'] ?></small>
                        <?php $posts = explode (",", $post['taglist']);
                                foreach ($posts as $singlevalue) {
                                echo "<a href=''>".$singlevalue."</a> &nbsp;";
                                }
                            ?>
                    </footer>
                </article>
                <?php } ?>                           
            </main>
        </div>
    </body>
</html>
