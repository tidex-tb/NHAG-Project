<div class="col-12 text-light" style="background-color: #555555;">
    <h2><?= $title ?></h2>
    <br>
    <style>
        /* CSS for images within cards */
        .card-img-top {
            margin: 0 auto;
            /* Center the image horizontally */
            max-width: 100%;
            /* Ensure the image doesn't exceed its container's width */
        }
    </style>
    <?php if (!empty($posts)) : ?>
        <?php $count = 0; ?>
        <div class="row">
            <?php foreach ($posts as $post) : ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <a href="<?= site_url('posts/' . $post['slug']); ?>">
                            <img class="card-img-top" src="<?= site_url(); ?>assets/images/posts/<?= $post['post_image']; ?>">
                        </a>

                        <div class="card-body">
                            <h5 class="card-title"><?= $post['title']; ?></h5>
                            <p class="card-text"><?= word_limiter($post['body'], ); ?><a href="<?= site_url('posts/' . $post['slug']); ?>">Read More</a></p>
                            <small class="text-muted">Posted on: <?= $post['created_at']; ?> in <strong><?= $post['name']; ?></strong></small>
                        </div>
                    </div>
                </div>
                <?php
                $count++;
                if ($count % 3 == 0) {
                    echo '</div><div class="row">';
                }
                ?>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p>No posts found.</p>
    <?php endif; ?>
</div>