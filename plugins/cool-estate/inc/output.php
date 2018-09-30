<?php

/**
 * @param int $count
 * Вывод недвижимости по шаблону
 */
function estate_output_template ( $count = 10 )
{
    $args = array (
        'numberposts' => $count ,
        'offset' => 0 ,
        'category' => 0 ,
        'orderby' => 'post_date' ,
        'order' => 'DESC' ,
        'include' => '' ,
        'exclude' => '' ,
        'meta_key' => '' ,
        'meta_value' => '' ,
        'post_type' => 'estate' ,
        'post_status' => 'publish' ,
        'suppress_filters' => true ,
    );

    $posts = wp_get_recent_posts( $args , OBJECT );
    estate_out( $posts );
}

/**
 * @param $posts
 * Шаблон вывода недвижимости
 */
function estate_out ( $posts )
{
    if ( empty( $posts ) ) {
        return false;
    }
    if ( !is_array( $posts ) ) {
        return false;
    }
    foreach ( $posts as $post ) {
        $img = get_the_post_thumbnail_url( $post->ID );
        $meta = get_post_meta( $post->ID );
        $link = get_permalink( $post->ID );
        $city = get_post( $meta[ 'estate_city' ][ 0 ] )->post_title;
        ?>

        <div class="col-md-6 pull-left">
            <div class="thumbnail">
                <img src="<?php echo $img ?>" alt="<?php echo $post->post_title ?>"
                     class="img-responsive">
                <div class="caption">

                    <h4 class="pull-right"><?php echo $meta[ 'price' ][ 0 ] ?> руб.</h4>
                    <h4><a href="<?php echo $link ?>"><?php echo $post->post_title ?> </a></h4>
                    <h5> <?php echo $city . ', ' . $meta[ 'address' ][ 0 ] ?></h5>

                    <table class="table table-bordered">
                        <tbody>
                        <tr>

                            <td>Этаж</td>
                            <td><?php echo $meta[ 'floor' ][ 0 ] ?></td>

                        </tr>
                        <tr>

                            <td>Площадь</td>
                            <td><?php echo $meta[ 'square' ][ 0 ] ?></td>
                        </tr>
                        <tr>

                            <td>Жилая Площадь</td>
                            <td><?php echo $meta[ 'living_space' ][ 0 ] ?></td>
                        </tr>

                        </tbody>
                    </table>
                </div>

                <div class="space-ten"></div>
                <div class="btn-ground text-center">
                    <a class="btn btn-primary" href="<?php echo $link ?>"><i
                            class="fa fa-search"></i> Просмотреть </a>
                </div>
                <div class="space-ten"></div>
            </div>
        </div>
        <?php
    }
}

/**
 * Вывод городов по шаблону
 */
function cities_output_template ()
{
    $args = array (
        'numberposts' => 0 ,
        'offset' => 0 ,
        'category' => 0 ,
        'orderby' => 'post_date' ,
        'order' => 'DESC' ,
        'include' => '' ,
        'exclude' => '' ,
        'meta_key' => '' ,
        'meta_value' => '' ,
        'post_type' => 'cities' ,
        'post_status' => 'draft, publish, future, pending, private' ,
        'suppress_filters' => true ,
    );

    $posts = wp_get_recent_posts( $args , OBJECT );
    cities_out( $posts );
}

/**
 * @param $posts
 * @return bool
 * Шаблон вывода городов
 */
function cities_out ( $posts )
{
    if ( empty( $posts ) ) {
        return false;
    }
    if ( !is_array( $posts ) ) {
        return false;
    }
    ?>
    <ul class="list-group">
        <li class="list-group-item" data-element="city-toggle" data-city-id="0">Показать все</li>
        <?php
        foreach ( $posts as $post ) {
            ?>
            <li class="list-group-item" data-element="city-toggle"
                data-city-id="<?php echo $post->ID ?>"><?php echo $post->post_title ?></li>
        <?php } ?>
    </ul>
    <?php
}

/**
 * Шаблон вывода недвижимости ( ответ AJAX )
 */
add_action( 'wp_footer' , 'city_switch' , 99 );
function city_switch ()
{
    ?>
    <script type="text/javascript">

        jQuery(document).ready(function () {


            jQuery('body').on('click', '[data-element="city-toggle"]', function () {
                let city_id = Number(jQuery(this).attr('data-city-id'));
                let data = {
                    action: 'city_search',
                    city_id
                };

                jQuery.post(myajax.url, data, function (response) {
                    // шаблон вывода через шаблонизатор LOdash
                    let tmpl = _.template(`
                    <div class="col-md-6 pull-left">
                        <div class="thumbnail">
                            <img src="<%=thumbnail%>" alt="<%=post_title%>"
                                 class="img-responsive">
                            <div class="caption">

                                <h4 class="pull-right"><%=meta.price%> руб.</h4>
                                <h4><a href="<%=link%>"><%=post_title%> </a></h4>
                                <h5> <%=city%>, <%=meta.address%></h5>

                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>

                                        <td>Этаж</td>
                                        <td><%=meta.floor%></td>

                                    </tr>
                                    <tr>

                                        <td>Площадь</td>
                                        <td><%=meta.square%></td>
                                    </tr>
                                    <tr>

                                        <td>Жилая Площадь</td>
                                        <td><%=meta.living_space%></td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>

                            <div class="space-ten"></div>
                            <div class="btn-ground text-center">
                                <a class="btn btn-primary" href="<%=link%>"><i
                                            class="fa fa-search"></i> Просмотреть </a>
                            </div>
                            <div class="space-ten"></div>
                        </div>
                    </div>

                    `);
                    let html = '';

                    response.forEach(function (post) {
                        html += tmpl(post);
                    });

                    jQuery('.estate .content_part').hide();
                    jQuery('.estate .content_part').html(html);
                    jQuery('.estate .content_part').show('slow');
                });

                return false;
            });
        });

    </script>
    <?php
}
