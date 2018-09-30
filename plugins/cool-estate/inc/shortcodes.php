<?php

/**
 * Выводим форму для создания категории
 * @return string
 */

function create_estate_form ( $atts )
{
    $terms  = get_terms( 'Тип недвижимости' , array (
        'hide_empty' => false ,
    ) );
    $args   = array (
        'numberposts' => 0 ,
        'category' => 0 ,
        'orderby' => 'date' ,
        'order' => 'DESC' ,
        'include' => array () ,
        'exclude' => array () ,
        'meta_key' => '' ,
        'meta_value' => '' ,
        'post_type' => 'cities' ,
    );
    $cities = get_posts( $args );
    ob_start();
    ?>

    <form id="thumbnail_upload" enctype="multipart/form-data" action="#" method="POST">
        <h3> Добавить недвижимость: </h3>
        <div class="form-group">
            <label for="post_title">Наименование объекта</label>
            <input type="text" name="post_title" class="form-control" id="post_title" required>
        </div>
        <div class="form-group">
            <label for="post_content">Описание </label>
            <textarea class="form-control" name="post_content" id="post_content" required></textarea>
        </div>
        <div class="form-group">
            <label for="estate_type">Тип недвижимости: </label>
            <select id="estate_type" name="estate_type">
                <?php foreach ( $terms as $term ) : ?>
                    <option value="<?php echo $term->name; ?>"><?php echo $term->name; ?></option>
                <?php endforeach; ?>
            </select>

        </div>
        <div class="form-group">
            <label for="estate_city">Город: </label>
            <select id="estate_city" name="estate_city">
                <?php foreach ( $cities as $city ) : ?>
                    <option value="<?php echo $city->ID; ?>"><?php echo $city->post_title; ?></option>
                <?php endforeach; ?>
            </select>

        </div>
        <div class="form-group">
            <label for="address">Адрес: </label>
            <input type="text" class="form-control" name="address" id="address" required>
        </div>
        <div class="form-group">
            <label for="floor">Этаж: </label>
            <input type="number" class="form-control" min="-2" name="floor" id="floor" required>
        </div>
        <div class="form-group">
            <label for="square">Площадь: </label>
            <input type="number" class="form-control" min="0" name="square" id="square" required>
        </div>
        <div class="form-group">
            <label for="living_space">Жилая площадь: </label>
            <input type="number" class="form-control" min="0" name="living_space" id="living_space" required>
        </div>
        <div class="form-group">
            <label for="living_space">Стоимость: </label>
            <input type="number" class="form-control" min="0" name="price" id="price" required>
        </div>
        <div class="from-trumb">
            Добавить миниатюру: <input name="thumbnail" type="file"/><br>
            Дополнительное изображение
            <a href="#" class="btn btn-info js-alerted">+</a>
            <br>
        </div>
        
        <!--        <input type="file" name="thumbnail" id="thumbnail">-->
        <?php wp_nonce_field( 'upload_thumb' , 'upload_thumb' ); ?>
        <input type="hidden" name="action" id="action" value="my_upload_action">
        <input id="submit-ajax" name="submit-ajax" class="btn btn-info" type="submit" value="Добавить">
        <div id="output1"></div>
    </form>
    <?php

    $out = ob_get_contents();
    ob_end_clean();

    return $out;

}


add_shortcode( 'estate_form' , 'create_estate_form' );



