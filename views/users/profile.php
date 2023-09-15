<ul>
    <li>ID: <?= $user->id ?></li>
    <li>Användarnamn: <?=  $user->username; ?></li>
    <?php if (isset($user->posts)) : ?>
        <li>Inlägg: 
            <ul>
                <?php foreach ($user->posts as $post) : ?>
                    <li><?= $post->body ?></li>
                <?php endforeach; ?>
            </ul>
        </li>
    <?php endif; ?>
</ul>

