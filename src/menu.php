
<?php
function print_menu($user_id)
{
echo '<nav id="menu">'
        .'<a href="news.php">Actualités</a>'
        .'<a href="wall.php?user_id='.$user_id.'">Mur</a>'
        .'<a href="feed.php?user_id='.$user_id.'">Flux</a>'
        .'<a href="tags.php?tag_id='.$user_id.'">Mots-clés</a>'
        /*.'<p>connected_id : <?php echo (isset($_SESSION["connected_id"]) ? $_SESSION["connected_id"] : 0) ?></p>'*/
        .'</nav>'
        .'<nav id="user">'
        .'<a href="#">Profil</a>'
        .'<ul>'
        .'<li><a href="settings.php?user_id='.$user_id.'">Paramètres</a></li>'
        .'<li><a href="followers.php?user_id='.$user_id.'">Mes suiveurs</a></li>'
        .'<li><a href="subscriptions.php?user_id='.$user_id.'">Mes abonnements</a></li>'
        .'</ul>'
        .'</nav>';
}
?>