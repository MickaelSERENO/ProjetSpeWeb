<?php
if (empty($_POST)) {
    echo "Aucune donn�e re�ue";
}
else {
    $post = print_r($_POST, true);
    file_put_contents("post_form.log", $post);
}
?>