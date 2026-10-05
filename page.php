<?php get_header(); ?>

<div class="main-content">
    <div class="container">
        <?php theme_breadcrumb(); ?>
        
        <div class="single-content">
            <h1 class="single-title"><?php the_title(); ?></h1>
            
            <div class="single-body">
                <?php the_content(); ?>
            </div>
            
            <?php comments_template(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>