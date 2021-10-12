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
                print_menu(isset($_SESSION['connected_id']) ? $_SESSION['connected_id'] : -1);
            ?> 
        </header>
        <div id="wrapper">
        <?php
            $mysqli = new mysqli("localhost:3306", "root", "root", "socialnetwork");
            $mysqli->set_charset("utf8mb4");

            $userEnSql = "SELECT users.id, posts_tags.tag_id FROM `users`"
            ." INNER JOIN `posts` ON posts.user_id = users.id"
            ." INNER JOIN `posts_tags` ON posts_tags.post_id = posts.id";

            $userInfo = $mysqli->query($userEnSql);
            $link = $userInfo->fetch_assoc();    

            $tagId = $link['tag_id'];
            $userId = $link['id'];
        ?>

            <aside>
                <?php
                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 */
                $laQuestionEnSql = "SELECT * FROM `users` WHERE id=" . intval($_SESSION['connected_id']);
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                ?>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les message des utilisatrices
                        auxquel est abonnée l'utilisatrice <?php echo $user['alias'] ?>
                        (n° <?php echo $_SESSION['connected_id'] ?>)
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
                        . "count(`likes`.`id`) as like_number,  "
                        . "GROUP_CONCAT(DISTINCT `tags`.`label`) AS taglist "
                        . "FROM `followers` "
                        . "JOIN `users` ON `users`.`id`=`followers`.`followed_user_id`"
                        . "JOIN `posts` ON `posts`.`user_id`=`users`.`id`"
                        . "LEFT JOIN `posts_tags` ON `posts`.`id` = `posts_tags`.`post_id`  "
                        . "LEFT JOIN `tags`       ON `posts_tags`.`tag_id`  = `tags`.`id` "
                        . "LEFT JOIN `likes`      ON `likes`.`post_id`  = `posts`.`id` "
                        . "WHERE `followers`.`following_user_id`='" . intval($userId) . "' "
                        . "GROUP BY `posts`.`id`"
                        . "ORDER BY `posts`.`created` DESC  "
                ;
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $post = $lesInformations->fetch_assoc();
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }
                while ($post = $lesInformations->fetch_assoc())
                {

                /**
                 * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
                 * A vous de retrouver comment faire la boucle while de parcours...
                 */
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
