<?php
namespace mp_ssv_events\widgets;
use mp_ssv_events\models\Event;
use mp_ssv_general\SSV_General;
use WP_Post;
use WP_Widget;

if (!defined('ABSPATH')) {
    exit;
}

class ssv_upcoming_events extends WP_Widget
{

    #region Construct
    public function __construct()
    {
        $widget_ops = array(
            'classname'                   => 'widget_events_cards',
            'description'                 => 'A list or dropdown for the first upcoming events per category.',
            'customize_selective_refresh' => true,
        );
        parent::__construct('upcoming_events', 'Upcoming Events', $widget_ops);
    }
    #endregion

    #region Widget
    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', empty($instance['title']) ? 'Upcoming Events' : $instance['title'], $instance, $this->id_base);

        $c = $instance['count'];

        echo $args['before_widget'];
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $taxonomy      = 'event_category';
        $postArgs       = array(
            'paged'          => get_query_var('paged'),
            'post_type'      => 'events',
            'meta_key'       => 'start',
            'meta_value'     => date("Y-m-d", time()),
            'orderby'        => 'meta_value',
            'groupby'        => 'meta_value',
            'meta_compare'   => '>=',
            'order'          => 'ASC',
        );
        $posts      = get_posts($postArgs);
        $categories = array();
        /** @var WP_Post $post */
        foreach ($posts as $post) {
            $terms = get_the_terms($post, $taxonomy);
            foreach (is_array($terms) ? $terms : array() as $term) {
                if (!isset($categories[$term->term_id])) {
                    $categories[$term->term_id] = $term;
                }
            }
        }

        foreach ($categories as $category) {
            $postArgs  = array(
                'posts_per_page' => $c,
                'paged'          => get_query_var('paged'),
                'post_type'      => 'events',
                'meta_key'       => 'start',
                'meta_value'     => date("Y-m-d", time()),
                'orderby'        => 'meta_value',
                'groupby'        => 'meta_value',
                'meta_compare'   => '>=',
                'order'          => 'ASC',
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'event_category',
                        'field'    => 'slug',
                        'terms'    => $category->name,
                    ),
                ),
            );
            $posts      = get_posts($postArgs);
            ?>
            <ul class="card-panel collection" style="padding: 0">
                <li class="collection-item" style="padding: 0 20px;">
                    <h3><a href="<?= esc_url(get_term_link($category, $taxonomy)) ?>" title="View all posts in <?= esc_html($category->name) ?>"><?= esc_html($category->name) ?></a></h3>
                </li>
                <?php foreach ($posts as $post): ?>
                    <li class="collection-item row" style="padding: 5px 0;">
                        <div class="col s4">
                            <?= (new Event($post))->getStart('d M') ?>
                        </div>
                        <div class="col s8">
                            <a href="<?= esc_url(get_permalink($post)) ?>"><?= $post->post_title ?></a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php
        }

        echo $args['after_widget'];
    }
    #endregion

    #region Update
    public function update($new_instance, $old_instance)
    {
        $instance          = $old_instance;
        $instance['title'] = SSV_General::sanitize($new_instance['title'], 'text');
        $instance['count'] = $new_instance['count'];

        return $instance;
    }
    #endregion

    #region Form
    public function form($instance)
    {
        //Defaults
        $instance = wp_parse_args((array)$instance, array('title' => ''));
        $title    = SSV_General::sanitize($instance['title'], 'text');
        $count    = $instance['count'];
        ?>
        <p>
            <label for="<?= esc_html($this->get_field_id('title')) ?>">Title:</label>
            <input class="widefat" id="<?= esc_html($this->get_field_id('title')) ?>" name="<?= esc_html($this->get_field_name('title')) ?>" type="text" value="<?= esc_html($title) ?>"/>
        </p>

        <p>
            <input type="number" class="number" id="<?= esc_html($this->get_field_id('count')) ?>" name="<?= esc_html($this->get_field_name('count')) ?>" value="<?= $count ?>"/>
            <label for="<?= esc_html($this->get_field_id('count')) ?>">Amount of Events per Category</label>
        </p>
        <?php
    }
    #endregion

}

add_action('widgets_init', create_function('', 'return register_widget("mp_ssv_events\widgets\ssv_upcoming_events");'));
