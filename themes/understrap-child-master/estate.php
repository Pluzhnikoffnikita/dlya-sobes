<?php /* Template Name: Объекты недвижимости */ ?>
<?php get_header(); ?>
<div class="container">
    <div class="row">
        <div class="pull-left estate col-md-9">
            <h2 class="text-center">Недвижимость</h2>

            <div class="content_part">
				<?php estate_output_template(); ?>
            </div>


        </div>
        <div class="pull-left cities col-md-3">
            <h2 class="text-center">Города</h2>
			<?php cities_output_template(); ?>

        </div>


    </div>
    <div id="form" class="row">
		<?php echo do_shortcode( '[estate_form]' ) ?>
    </div>
</div>

<?php get_footer(); ?>
