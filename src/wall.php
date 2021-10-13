<?php
session_start(); 
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mur</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
            <img src="resoc.jpg" alt="Logo de notre réseau social"/>
            <?php

            $mysqli = new mysqli("localhost:3306", "root", "root", "socialnetwork");
            $mysqli->set_charset("utf8mb4");
            ?>
            <?php
                include("menu.php");
                print_menu(isset($_SESSION['connected_id']) ? $_SESSION['connected_id'] : 0);
            ?> 
        </header>
        <div id="wrapper">
            <?php
            /**
             * Etape 1: Le mur concerne un utilisateur en particulier
             * La première étape est donc de trouver quel est l'id de l'utilisateur
             * Celui ci est indiqué en parametre GET de la page sous la forme user_id=...
             * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
             * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
             */
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
                    <p>Sur cette page vous trouverez tous les message de l'utilisatrice : <?php echo $user['alias'] ?>
                        (n° <?php echo $userId ?>)
                    </p>
                </section>
            </aside>
            <main>
                <?php
                    $likesEnCoursDeTraitement = isset($_POST['post_id']);
                    if ($likesEnCoursDeTraitement)
                    {
                        // on ne fait ce qui suit que si un formulaire a été soumis.
                        $postLike = $_POST['post_id'];

                        // Petite sécuritén - pour éviter les injection sql :
                        $postLike = $mysqli->real_escape_string($postLike);

                        // Construction de la requete
                        $lInstructionSql = "INSERT INTO `likes` "
                                . "(`id`, `user_id`, `post_id`) "
                                . "VALUES (NULL, "
                                . "" . $_SESSION["connected_id"] . ", "
                                . "" . $postLike . ");";
                        
                        // Execution
                        $ok = $mysqli->query($lInstructionSql);
                        if ( ! $ok)
                        {
                            echo "Impossible d'ajouter un like: " . $mysqli->error;
                        } else
                        {
                            echo "like posté";
                        }
                    }
                    
                ?>

                <?php
                    $enCoursDeTraitement = isset($_POST['message']);
                    if ($enCoursDeTraitement)
                    {
                        // on ne fait ce qui suit que si un formulaire a été soumis.
                        $postContent = $_POST['message'];
                        $tagLabel = $_POST['tag'];
                        
                        // Petite sécuritén - pour éviter les injection sql : https://www.w3schools.com/sql/sql_injection.asp
                        $postContent = $mysqli->real_escape_string($postContent);
                        $tagLabel = $mysqli->real_escape_string($tagLabel);

                        // Construction de la requete
                        $lInstructionSql = "INSERT INTO `posts` "
                                . "(`id`, `user_id`, `content`, `created`) "
                                . "VALUES (NULL, "
                                . "" . $_SESSION["connected_id"] . ", "
                                . "'" . $postContent . "', "
                                . "NOW());"
                                . "";
                        // tag
                        $lInstructionSqlTags = "INSERT INTO `tags` "
                                . "(`id`, `label`) "
                                . "VALUES (NULL, "
                                . "'" . $tagLabel . "');";
                        // post-tag
                        $lInstructionSqlPostTags = "INSERT INTO `posts_tags` "
                                . "(`id`, `post_id`, `tag_id`) "
                                . "VALUES (NULL, "
                                . "(SELECT `id` FROM `posts` WHERE `content` ='" . $postContent . "'), "
                                . "(SELECT `id` FROM `tags` WHERE `label` ='" . $tagLabel . "'));";
                        // Execution
                        $ok = $mysqli->query($lInstructionSql);
                        $ok = $ok and $mysqli->query($lInstructionSqlTags);
                        if ( ! $ok)
                        {
                            echo "Impossible d'ajouter le message: " . $mysqli->error;
                        } else
                        {
                            echo "Message posté";
                            print_r($lInstructionSqlTags);
                            print_r($lInstructionSqlPostTags);
                        }
                    }
                    ?>
                <?php
                /**
                 * Etape 3: récupérer tous les messages de l'utilisatrice
                 */
                $laQuestionEnSql = "SELECT `posts`.`content`,"
                        . "`posts`.`created`,"
                        . "`users`.`alias` as author_name,  "
                        . "`posts`.`id` as post_id,  "
                        . "count(DISTINCT `likes`.`id`) as like_number,  "
                        . "GROUP_CONCAT(DISTINCT `tags`.`label`) AS taglist "
                        . "FROM `posts`"
                        . "JOIN `users` ON  `users`.`id`=`posts`.`user_id`"
                        . "LEFT JOIN `posts_tags` ON `posts`.`id` = `posts_tags`.`post_id`  "
                        . "LEFT JOIN `tags`       ON `posts_tags`.`tag_id`  = `tags`.`id` "
                        . "LEFT JOIN `likes`      ON `likes`.`post_id`  = `posts`.`id` "
                        . "WHERE `posts`.`user_id`='" . intval($userId) . "' "
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
                        <address>par <?php echo $user['alias'] ?></address>
                        <div>
                            <p><?php echo $post['content'] ?></p>
                        </div>                                            
                        <footer>
                            <small><form action="wall.php" method="post">
                             <input type='hidden' name='post_id' value='<?php echo $post['post_id'] ?>'>
                             <input type="submit" value="like">
                                ♥ <?php echo $post['like_number'] ?>
                             </form>
                            </small>
                            <?php $posts = explode (",", $post['taglist']);
                                foreach ($posts as $singlevalue) {
                                echo "<a href=''>".$singlevalue."</a> &nbsp;";
                                }
                            ?>
                        </footer>
                    </article>
                <?php }
                /**
                 * Poster un nouveau message 
                 */
                    // Vérifier si on est en train d'afficher ou de traiter le formulaire
                    // si on recoit un champs email rempli il y a une chance que ce soit un traitement
                    ?>                     
                    <form action="wall.php" method="post">
                        <input type='hidden' name='newPost' value='validate'>
                        <dl>
                            <dt><label for='message'>Message</label></dt>
                            <dd><textarea name='message'></textarea></dd>
                        </dl>
                        <input type='hidden' name='tag' value='validate'>
                        <dl>
                            <dt><label for='tag'>Mot-clé</label></dt>
                            <dd><textarea name='tag'></textarea></dd>
                        </dl>
                        <input type='submit'>

            </main>
        </div>
    </body>
</html>
