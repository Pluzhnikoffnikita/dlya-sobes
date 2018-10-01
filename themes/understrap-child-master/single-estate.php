<?php get_header();



?>
<?php if ( have_posts() ) :  while ( have_posts() ) : the_post();
	$images = explode(',',get_field( 'gallery' ));
	$meta   = get_post_meta( get_the_ID() );
?>





    <div class="container">

        <div class="row">
            <h1> <?php the_title() ?></h1>
            <?php the_post_thumbnail() ?>

        </div>
        <div class="meta">
            <h4> Информация: </h4>
            <p><strong>Адрес: </strong><?php echo $meta['address'][0] ?></p>
            <p><strong>Этаж: </strong><?php echo $meta['floor'][0] ?></p>
            <p><strong>Площадь: </strong><?php echo $meta['square'][0] ?></p>
            <p><strong>Жилая площадь: </strong><?php echo $meta['living_space'][0] ?></p>

        </div>
		<?php if ( strlen( get_the_content() ) > 0 ) : ?>
            <div class="row description">

                <div class="col-md-12">
                    <h3> Описание: </h3><hr>
                    <?php the_content() ?>
                </div>
            </div>
		<?php endif; ?>
		<?php
        if ( $images ): ?>

            <h1 class="h3 text-center my-4">Галерея: </h1>
            <div class="row">

				<?php foreach ( $images as $key => $image ):
                    $img = wp_get_attachment_image_src((int)$image,'medium');
                    ?>

                    <div class="col-lg-3 col-md-4 col-6 thumb">
                        <a data-fancybox="gallery" href="<?php echo $img[0]; ?>">
                            <img class="img-fluid" src="<?php echo $img[0] ?>"
                                 alt="">
                        </a>
                    </div>

				<?php endforeach; ?>

            </div>
		<?php endif; ?>
    </div>

<?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>