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
            <nav id="menu">
                <a href="news.php">Actualités</a>
                <a href="wall.php?user_id=<?php echo 1 ?>">Mur</a>
                <a href="feed.php?user_id=<?php echo 1 ?>">Flux</a>
                <a href="tags.php?tag_id=<?php echo 1 ?>">Mots-clés</a>
            </nav>
            <nav id="user">
                <a href="#">Profil</a>
                <ul>
                    <li><a href="settings.php?user_id=<?php echo 1 ?>">Paramètres</a></li>
                    <li><a href="followers.php?user_id=<?php echo 1 ?>">Mes suiveurs</a></li>
                    <li><a href="subscriptions.php?user_id=<?php echo 1 ?>">Mes abonnements</a></li>
                </ul>

            </nav>
        </header>
        <div id="wrapper">
            <?php
            /**
             * Cette page est similaire à wall.php ou feed.php 
             * mais elle porte sur les mots-clés (tags)
             */
            /**
             * Etape 1: Le mur concerne un mot-clé en particulier
             */
            $tagId = $_GET['tag_id'];
            $userId = 1;
            ?>
            <?php
            /**
             * Etape 2: se connecter à la base de donnée
             */
            $mysqli = new mysqli("localhost:3307", "root", "", "socialnetwork");
            $mysqli->set_charset("utf8mb4");
            ?>

            <aside>
                <?php
                /**
                 * Etape 3: récupérer le nom du mot-clé
                 */
                $laQuestionEnSql = "SELECT * FROM `tags` WHERE id=" . intval($tagId);
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $tag = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par le label et effacer la ligne ci-dessous
                ?>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les derniers messages comportant
                        le mot-clé <?php echo $tag['label'] ?>
                        (n° <?php echo $_GET['tag_id'] ?>)
                    </p>

                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 3: récupérer tous les messages avec un mot clé donné
                 */
                $laQuestionEnSql = "SELECT `posts`.`content`,"
                        . "`posts`.`created`,"
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