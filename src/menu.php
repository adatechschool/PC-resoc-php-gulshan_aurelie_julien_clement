
<?php
function print_menu($user_id)
{
        if (is_null($user_id) or $user_id == 0) {
                $menu = '<nav id="menu">'
                        . '<a href="news.php">Actualités</a>'
                        . '<a href="tags.php">Mots-clés</a>'
                        . '</nav>'
                        . '<nav id="user">'
                        . '<a href="#">Profil</a>'
                        . '<ul>'
                        . '<li><a href="login.php">Login</a></li>'
                        . '</ul>'
                        . '</nav>';
        } else {
                $menu = '<nav id="menu">'
                        . '<a href="news.php">Actualités</a>'
                        . '<a href="wall.php">Mur</a>'
                        . '<a href="feed.php">Flux</a>'
                        . '<a href="tags.php">Mots-clés</a>'
                        . '</nav>'
                        . '<nav id="user">'
                        . '<a href="#">Profil</a>'
                        . '<ul>'
                        . '<li><a href="settings.php?user_id=' . $user_id . '">Paramètres</a></li>'
                        . '<li><a href="followers.php?user_id=' . $user_id . '">Mes suiveurs</a></li>'
                        . '<li><a href="subscriptions.php?user_id=' . $user_id . '">Mes abonnements</a></li>'
                        . '<li><a href="login.php">Se déconnecter</a></li>'
                        . '</ul>'
                        . '</nav>';
        }
        echo $menu;
}