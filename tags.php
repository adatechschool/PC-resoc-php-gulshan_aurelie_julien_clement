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
                include("menu.php");
                print_menu(isset($_SESSION['connected_id']) ? $_SESSION['connected_id'] : 0);
            ?> 

            <?php
                $mysqli = new mysqli("localhost:3306", "root", " ", "socialnetwork");
                $mysqli->set_charset("utf8mb4");

        //verification de la connexion
                if ($mysqli->connect_errno)
                {
                    echo("Échec de la connexion : " . $mysqli->connect_error);
                    exit();
                }
            ?>

        </header>
        <div id="wrapper">
            <aside>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les mots-clés identifiés sur ce
                        reseau social : 
                    </p>
                </section>
            </aside>

            <main>
                <?php
                /**
                 * Récupérer tous les mots-clés
                 */
                    $laQuestionEnSql = "SELECT * FROM `tags`";
                    $lesInformations = $mysqli->query($laQuestionEnSql);
                // Vérification
                    if ( ! $lesInformations)
                    {
                        echo("Échec de la requete : " . $mysqli->error);
                        exit();
                    }

                    while ($tag = $lesInformations->fetch_assoc())
                    {
                    ?>
                    <article>
                        <h3><?php echo $tag['label'] ?></h3>
                        <p>id:<?php echo $tag['id'] ?></p>
                        <nav>
                            <a href="tags.php?tag_id=<?php echo $tag['id']?>">Messages</a>
                        </nav>
                    </article>
                <?php } ?>


            </main>
        </div>
    </body>
</html>