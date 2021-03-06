<?php
session_start(); 
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Actualités</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
            <a href='admin.php'><img src="resoc.jpg" alt="Logo de notre réseau social"/></a>
            <?php
                include("menu.php");
                //print_menu(isset($_SESSION['connected_id']) ? $_SESSION['connected_id'] : 0);
                print_menu(isset($_SESSION['connected_id']) ? $_SESSION['connected_id'] : 0);
            ?> 
        </header>
        <div id="wrapper">
            <aside>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les derniers messages de
                        tous les utilisatrices du site.</p>
                </section>
            </aside>
            <main>
                
                <?php
                /*
                  // Documentation : les exemples https://www.php.net/manual/fr/mysqli.query.php
                  // plus généralement : https://www.php.net/manual/fr/mysqli.query.php
                 */

                // Etape 1: Ouvrir une connexion avec la base de donnée.
                $mysqli = new mysqli("localhost:3306", "root", "root", "socialnetwork");
                $mysqli->set_charset("utf8mb4"); 
                //verification
                if ($mysqli->connect_errno)
                {
                    echo("Échec de la connexion : " . $mysqli->connect_error);
                    echo("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
                    exit();
                }
                
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
                // Etape 2: Poser une question à la base de donnée et récupérer ses informations
                // cette requete vous est donnée, elle est complexe mais correcte, 
                // si vous ne la comprenez pas c'est normal, passez, on y reviendra
                $laQuestionEnSql = "SELECT `posts`.`content`,"
                        . "`posts`.`created`,"
                        . "`users`.`alias` as author_name,  "
                        . "`posts`.`id` as post_id,  "
                        . "count(`likes`.`id`) as like_number,  "
                        . "GROUP_CONCAT(DISTINCT `tags`.`label`) AS taglist "
                        . "FROM `posts`"
                        . "JOIN `users` ON  `users`.`id`=`posts`.`user_id`"
                        . "LEFT JOIN `posts_tags` ON `posts`.`id` = `posts_tags`.`post_id`  "
                        . "LEFT JOIN `tags`       ON `posts_tags`.`tag_id`  = `tags`.`id` "
                        . "LEFT JOIN `likes`      ON `likes`.`post_id`  = `posts`.`id` "
                        . "GROUP BY `posts`.`id`"
                        . "ORDER BY `posts`.`created` DESC  "
                        . "LIMIT 5";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                // Vérification
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                    echo("<p>Indice: Vérifiez les la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
                    exit();
                }

                // Etape 3: Parcourir ces données et les ranger bien comme il faut dans du html
                // NB: à chaque tour du while, la variable post ci dessous reçois les informations du post suivant.
                while ($post = $lesInformations->fetch_assoc())
                {
                    // @todo : Votre mission c'est de remplacer les AREMPLACER par les bonnes valeurs
                    // ci-dessous par les bonnes valeurs cachées dans la variable $post 
                    // on vous met le pied à l'étrier avec created
                    // 
                    // avec le ? > ci-dessous on sort du mode php et on écrit du html comme on veut... mais en restant dans la boucle
                    ?>
                    <article>
                        <h3>
                            <time><?php echo $post['created'] ?></time>
                        </h3>
                        <address><?php echo $post['author_name'] ?></address>
                        <div>
                            <p><?php echo $post['content'] ?></p>
                        </div>
                        <footer>
                            <small><form action="news.php" method="post">
                             <input type='hidden' name='post_id' value='<?php echo $post['post_id'] ?>'>
                             <input type="submit" value="like">
                                ♥ <?php echo $post['like_number'] ?>
                             </form></small>
                            <?php $tags = explode (",", $post['taglist']);
                                foreach ($tags as $singlevalue) {
                                echo "<a href=''>".$singlevalue."</a> &nbsp;";
                                }
                            ?>
                        </footer>
                    </article>
                    <?php
                    // avec le <?php ci-dessus on retourne en mode php 
                }// cette accolade ferme et termine la boucle while ouverte avant.
                ?>

            </main>
        </div>
    </body>
</html>
