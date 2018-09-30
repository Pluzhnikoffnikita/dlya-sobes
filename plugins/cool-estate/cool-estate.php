<?php
/*
Plugin Name: Cool Estate
Description: Allow to create estate objects
Version: 1.3
Author: Pluzhnikov Nikita
License: GPL2

*/
// Подключаем шорткоды
include 'inc/shortcodes.php';

// Подключаем шаблоны вывода
include 'inc/output.php';

/**
 * Добавляем тип поста "Недвижимость"
 */
add_action( 'wp_enqueue_scripts' , 'script_for_coolestate');
function script_for_coolestate(){
	wp_enqueue_script( 'scripts' , plugin_dir_url(__FILE__) . 'assets/js/scripts.js' , 'jquery', '1', true  );
}

add_action( 'init', 'menu_init_estate' );

function menu_init_estate() {
	$labels = array(
		'name'               => 'Недвижимость',
		'singular_name'      => 'Недвижимость',
		'add_new'            => 'Добавить Недвижимость',
		'add_new_item'       => 'Добавить Недвижимость',
		'edit_item'          => 'Редактировать Недвижимость ',
		'new_item'           => 'Новая Недвижимость',
		'all_items'          => 'Вся Недвижимость',
		'view_item'          => 'Просмотреть Недвижимость',
		'search_items'       => 'Поиск по Недвижимости',
		'not_found'          => 'Не найдено Недвижимости',
		'not_found_in_trash' => 'Не найдено в корзине',
		'menu_name'          => 'Недвижимость',
	);


	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => true,
		'capability_type'    => 'post',
		'has_archive'        => true,
		'menu_icon'          => 'dashicons-admin-multisite',
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array(
			'title',
			'editor',
			'thumbnail',
			'attachment'
		)
	);

	register_post_type( 'estate', $args );


}


/**
 * Добавляем тип поста "Город"
 */
add_action( 'init', 'menu_init_cities' );

function menu_init_cities() {
	$labels = array(
		'name'               => 'Город',
		'singular_name'      => 'Город',
		'add_new'            => 'Добавить город',
		'add_new_item'       => 'Добавить город',
		'edit_item'          => 'Редактировать город',
		'new_item'           => 'Новый город',
		'all_items'          => 'Все города',
		'view_item'          => 'Просмотреть город',
		'search_items'       => 'Поиск по городу',
		'not_found'          => 'Не найдено городов',
		'not_found_in_trash' => 'Не найдено городов',
		'menu_name'          => 'Города',
	);


	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => true,
		'capability_type'    => 'post',
		'has_archive'        => true,
		'menu_icon'          => 'dashicons-admin-site',
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array(
			'title',
			'editor',
			'thumbnail',


		)
	);

	register_post_type( 'cities', $args );


}

/**
 * регистрируем таксономии
 */

add_action( 'init', 'create_estate_types', 0 );
function create_estate_types() {

	$labels = array(
		'name'              => __( 'Тип недвижимости' ),
		'singular_name'     => __( 'Тип недвижимости' ),
		'search_items'      => __( 'Поиск по типу недвижимости' ),
		'all_items'         => __( 'Типы недвижимости' ),
		'parent_item'       => __( '' ),
		'parent_item_colon' => __( '' ),
		'edit_item'         => __( 'Редактировать тип недвижимости' ),
		'update_item'       => __( 'Обновить тип недвижимости' ),
		'add_new_item'      => __( 'Добавить тип недвижимости' ),
		'new_item_name'     => __( 'Новый тип недвижимости' ),
		'menu_name'         => __( 'Типы недвижимости' ),
	);


	register_taxonomy( 'Тип недвижимости', array( 'estate' ), array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'estate_type' ),
		'show_admin_column' => true,
	) );


}

/**
 * Добавляем мета боксы
 *
 */

add_action( 'add_meta_boxes', 'estate_boxes', 1 );

function estate_boxes() {
	add_meta_box( 'estate_extra_fields', 'Данные объекта: ', 'estate_fields_func', 'estate', 'normal', 'high' );
}

function estate_fields_func( $post ) {
	$args = array(
		'numberposts'      => 0,
		'category'         => 0,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'include'          => array(),
		'exclude'          => array(),
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        => 'cities',
	);

	$posts       = get_posts( $args );
	$current_val = get_post_meta( $post->ID, 'estate_city', true );


	?>

    Выберите город:
    <select name="estate_city">
        <option value="0"> Выберите город...</option>
		<?php foreach ( $posts as $post ) { ?>
            <option
				<?php selected( $current_val, $post->ID ); ?> value="<?php echo $post->ID ?>"><?php echo $post->post_title ?></option>
		<?php } ?>
    </select>
	<?php

}

/**
 * @param $post_id
 * @return bool
 * Сохраняем связь недвижимость - город
 */
function save_city( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	} // выходим если это автосохранение
	if ( !current_user_can( 'edit_post', $post_id ) ) {
		return false;
	} // выходим если юзер не имеет право редактировать запись
	$city_id = (int)$_POST['estate_city'];
	if ( $city_id > 0 ) {
		update_post_meta( $post_id, 'estate_city', $city_id );
	}

	return true;
}

add_action( 'save_post', 'save_city' );




add_action( 'wp_footer', 'ajax_add_estate', 99 );

function ajax_add_estate() {
	?>
    <script>
        jQuery(document).ready(function ($) {
            let options = {
                target: '#output1',
                beforeSubmit: showRequest,
                success: showResponse,
                url: myajax.url,
            };
            // Подвязываем асинхронную обработку формы
            jQuery('#thumbnail_upload').ajaxForm(options);
        });

        function showRequest(formData, jqForm, options) {
            jQuery('#output1').html('Sending...');

        }

        function showResponse(responseText, statusText, xhr, $form) {
            jQuery('#output1').html('Успешно добавлен объект!');
            jQuery("#thumbnail_upload")[0].reset();
        }

    </script>
	<?php
}

/**
 * Функция добавления недвижимости с фронтенда
 */
add_action( 'wp_ajax_my_upload_action', 'ajax_post_estate' );

add_action( 'wp_ajax_nopriv_my_upload_action', 'ajax_post_estate' );

function ajax_post_estate() {

	if ( !wp_verify_nonce( $_POST['upload_thumb'], 'upload_thumb' ) ) {
		die();
	}
    // заполняем базовые поля
	$post_data = array(
		'post_title'   => wp_strip_all_tags( $_POST['post_title'] ),
		'post_content' => wp_strip_all_tags($_POST['post_content'] ),
		'post_status'  => 'draft',
		'post_author'  => $_POST['post_author'],
		'post_type'    => 'estate'

	);
	// Тип недвижимости
	$type      = esc_sql( $_POST['estate_type'] );

	if ( !get_term_by( 'name', $type, 'Тип недвижимости' ) ) {
		wp_send_json( [ 'error' => 'Не найден тип недвижимости' ] );
	}

    // Вставляем запись в базу данных
	$post_id = wp_insert_post( $post_data, true );
    // Добавляем тип недвижимости и остальные мета
	wp_set_object_terms( $post_id, $type, 'Тип недвижимости', false );
	update_post_meta( $post_id, 'square', (int)$_POST['square'] );
	update_post_meta( $post_id, 'floor', (int)$_POST['floor'] );
	update_post_meta( $post_id, 'living_space', (int)$_POST['living_space'] );
	update_post_meta( $post_id, 'address', esc_sql( wp_strip_all_tags($_POST['address']) ) );
	update_post_meta( $post_id, 'estate_city', (int)$_POST['estate_city'] );
	update_post_meta( $post_id, 'price', (int)$_POST['price'] );

    // обрабатываем присланные через форму файлы

	require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
	require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
	require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
    $gallery = array();
	if ( $_FILES ) {
		foreach ( $_FILES as $file => $array ) {
			if ( $_FILES[$file]['error'] !== UPLOAD_ERR_OK ) {
				echo "upload error : " . $_FILES[$file]['error'];
				die();

			}

			$attach_id = media_handle_upload( $file, $post_id );
			//если фото отправлено как миниатюра - добавляем миниатюру
			if ( $file == 'thumbnail' ) {
				update_post_meta( $post_id, '_thumbnail_id', $attach_id );
			}
			else {
			    $gallery[] = $attach_id;
            }
            // добавляем дополнительные фото
			update_post_meta( $post_id, 'gallery', $gallery );

		}
	}

	die();
}


/**
 * Меняем чекбоксы у типов недвижимости на радио
 */


if ( !class_exists( 'WDS_Taxonomy_Radio' ) ) {
	/**
	 * Removes and replaces the built-in taxonomy metabox with our radio-select metabox.
	 * @link  http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
	 */
	class WDS_Taxonomy_Radio {

		// Post types where metabox should be replaced (defaults to all post_types associated with taxonomy)
		public $post_types = array();
		// Taxonomy slug
		public $slug = '';
		// Taxonomy object
		public $taxonomy = false;
		// New metabox title. Defaults to Taxonomy name
		public $metabox_title = '';
		// Metabox priority. (vertical placement)
		// 'high', 'core', 'default' or 'low'
		public $priority = 'high';
		// Metabox position. (column placement)
		// 'normal', 'advanced', or 'side'
		public $context = 'side';
		// Set to true to hide "None" option & force a term selection
		public $force_selection = false;


		/**
		 * Initiates our metabox action
		 *
		 * @param string $tax_slug Taxonomy slug
		 * @param array $post_types post-types to display custom metabox
		 */
		public function __construct( $tax_slug, $post_types = array() ) {

			$this->slug       = $tax_slug;
			$this->post_types = is_array( $post_types ) ? $post_types : array( $post_types );

			add_action( 'add_meta_boxes', array( $this, 'add_radio_box' ) );
		}

		/**
		 * Removes and replaces the built-in taxonomy metabox with our own.
		 */
		public function add_radio_box() {
			foreach ( $this->post_types() as $key => $cpt ) {
				// remove default category type metabox
				remove_meta_box( $this->slug . 'div', $cpt, 'side' );
				// remove default tag type metabox
				remove_meta_box( 'tagsdiv-' . $this->slug, $cpt, 'side' );
				// add our custom radio box
				add_meta_box( $this->slug . '_radio', $this->metabox_title(), array(
					$this,
					'radio_box'
				), $cpt, $this->context, $this->priority );
			}
		}

		/**
		 * Displays our taxonomy radio box metabox
		 */
		public function radio_box() {

			// uses same noncename as default box so no save_post hook needed
			wp_nonce_field( 'taxonomy_' . $this->slug, 'taxonomy_noncename' );

			// get terms associated with this post
			$names = wp_get_object_terms( get_the_ID(), $this->slug );
			// get all terms in this taxonomy
			$terms = (array)get_terms( $this->slug, 'hide_empty=0' );
			// filter the ids out of the terms
			$existing = ( !is_wp_error( $names ) && !empty( $names ) )
				? (array)wp_list_pluck( $names, 'term_id' )
				: array();
			// Check if taxonomy is hierarchical
			// Terms are saved differently between types
			$h = $this->taxonomy()->hierarchical;

			// default value
			$default_val = $h ? 0 : '';
			// input name
			$name = $h ? 'tax_input[' . $this->slug . '][]' : 'tax_input[' . $this->slug . ']';

			echo '<div style="margin-bottom: 5px;">
         <ul id="' . $this->slug . '_taxradiolist" data-wp-lists="list:' . $this->slug . '_tax" class="categorychecklist form-no-clear">';

			// If 'category,' force a selection, or force_selection is true
			if ( $this->slug != 'category' && !$this->force_selection ) {
				// our radio for selecting none
				echo '<li id="' . $this->slug . '_tax-0"><label><input value="' . $default_val . '" type="radio" name="' . $name . '" id="in-' . $this->slug . '_tax-0" ';
				checked( empty( $existing ) );
				echo '> Не указан</label></li>';
			}

			// loop our terms and check if they're associated with this post
			foreach ( $terms as $term ) {

				$val = $h ? $term->term_id : $term->slug;

				echo '<li id="' . $this->slug . '_tax-' . $term->term_id . '"><label><input value="' . $val . '" type="radio" name="' . $name . '" id="in-' . $this->slug . '_tax-' . $term->term_id . '" ';
				// if so, they get "checked"
				checked( !empty( $existing ) && in_array( $term->term_id, $existing ) );
				echo '> ' . $term->name . '</label></li>';
			}
			echo '</ul></div>';

		}

		/**
		 * Gets the taxonomy object from the slug
		 * @return object Taxonomy object
		 */
		public function taxonomy() {
			$this->taxonomy = $this->taxonomy ? $this->taxonomy : get_taxonomy( $this->slug );

			return $this->taxonomy;
		}

		/**
		 * Gets the taxonomy's associated post_types
		 * @return array Taxonomy's associated post_types
		 */
		public function post_types() {
			$this->post_types = !empty( $this->post_types ) ? $this->post_types : $this->taxonomy()->object_type;

			return $this->post_types;
		}

		/**
		 * Gets the metabox title from the taxonomy object's labels (or uses the passed in title)
		 * @return string Metabox title
		 */
		public function metabox_title() {
			$this->metabox_title = !empty( $this->metabox_title ) ? $this->metabox_title : $this->taxonomy()->labels->name;

			return $this->metabox_title;
		}


	}

	$custom_tax_mb = new WDS_Taxonomy_Radio( 'Тип недвижимости', array( 'estate' ) );


}








