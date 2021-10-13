<?php
session_start(); 
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Les message par mot-clé</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
            <img src="resoc.jpg" alt="Logo de notre réseau social"/>

            <?php
            $mysqli = new mysqli("localhost:3306", "root", "root", "socialnetwork");
            $mysqli->set_charset("utf8mb4");
            
        //verification de la connexion
            if ($mysqli->connect_errno)
            {
                echo("Échec de la connexion : " . $mysqli->connect_error);
                exit();
            }

            include("menu.php");
            print_menu(isset($_SESSION['connected_id']) ? $_SESSION['connected_id'] : 0);
            ?> 

        </header>
        <div id="wrapper">
            <?php
            $tagId = $_GET['tag_id'];
            ?>

            <aside>

                <?php
                /**
                 * Récupérer le mot-clé
                 */
                $laQuestionEnSql = "SELECT * FROM `tags` WHERE id=" . intval($tagId);
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $tag = $lesInformations->fetch_assoc();
                ?>

                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les derniers messages comportant
                        le mot-clé
                        (n° <?php echo $_GET['tag_id'] ?>)
                    </p>
                </section>
            </aside>

            <main>
                <?php
                /**
                 * Récupérer tous les messages avec un mot clé donné
                 */
                $laQuestionEnSql = "SELECT `posts`.`content`,"
                        . "`posts`.`created`,"
                        . "`posts`.`user_id`,  "
                        . "`users`.`alias` as author_name,  "
                        . "count(`likes`.`id`) as like_number,  "
                        . "GROUP_CONCAT(DISTINCT `tags`.`label`) AS taglist "
                        . "FROM `posts_tags` as filter "
                        . "JOIN `posts` ON `posts`.`id`=filter.`post_id`"
                        . "JOIN `users` ON `users`.`id`=`posts`.`user_id`"
                        . "LEFT JOIN `posts_tags` ON `posts`.`id` = `posts_tags`.`post_id`  "
                        . "LEFT JOIN `tags`       ON `posts_tags`.`tag_id`  = `tags`.`id` "
                        . "LEFT JOIN `likes`      ON `likes`.`post_id`  = `posts`.`id` "
                        . "WHERE filter.`tag_id`='" . intval($tagId) . "' "
                        . "GROUP BY `posts`.`id`"
                        . "ORDER BY `posts`.`created` DESC  "
                ;

                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }

                /**
                 * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
                 */
                while ($post = $lesInformations->fetch_assoc())
                {
                ?>                
                    <article>
                        <h3>
                            <time datetime='<?php echo $post['created'] ?>' ><?php echo $post['created'] ?></time>
                        </h3>
                        <address>par 
                        <a href="wall.php?user_id=<?php echo $post['user_id'] ?>"><?php echo $post['author_name'] ?></a>
                        </address>
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