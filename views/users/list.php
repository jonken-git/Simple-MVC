<?php
foreach($users as $user) {
    echo "<div class='user'>";
    echo "<h2>" . $user->username . "</h2>";
    echo "<p>" . $user->email . "</p>";
    echo "<a href='/users/profile/" . $user->id . "'>Profile</a>";
    echo "</div>";
    echo "<hr>";
}