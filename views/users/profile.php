<ul>
    <li>ID: <?= $user->id ?></li>
    <li>Användarnamn: <?=  $user->username; ?></li>
    <li>Fullnamn: <?= $user->getFullName() ?></li>
    <?php if (isset($user->posts)) : ?>
        <li>Inlägg: 
            <ul>
                <?php foreach ($user->posts as $post) : ?>
                    <li><?= $post->body ?></li>
                <?php endforeach; ?>
            </ul>
        </li>
    <?php endif; ?>
    <?php if (isset($user->comments)) : ?>
        <li>Inlägg: 
            <ul>
                <?php foreach ($user->comments as $comment) : ?>
                    <li><?= $comment->content ?></li>
                <?php endforeach; ?>
            </ul>
        </li>
    <?php endif; ?>
</ul>

