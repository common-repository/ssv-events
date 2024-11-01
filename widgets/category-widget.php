<?php
namespace mp_ssv_events\widgets;
use mp_ssv_general\SSV_General;
use WP_Widget;

if (!defined('ABSPATH')) {
    exit;
}

class ssv_event_category extends WP_Widget
{

    #region Construct
    public function __construct()
    {
        $widget_ops = array(
            'classname'                   => 'widget_event_categories',
            'description'                 => 'A list or dropdown of event categories.',
            'customize_selective_refresh' => true,
        );
        parent::__construct('event_categories', 'Event Categories', $widget_ops);
    }
    #endregion

    #region Widget
    public function widget($args, $instance)
    {
        static $first_dropdown = true;

        $title = apply_filters('widget_title', empty($instance['title']) ? 'Event Categories' : $instance['title'], $instance, $this->id_base);

        $c = !empty($instance['count']) ? '1' : '0';
        $h = !empty($instance['hierarchical']) ? '1' : '0';
        $d = !empty($instance['dropdown']) ? '1' : '0';

        echo $args['before_widget'];
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $cat_args = array(
            'orderby'      => 'name',
            'show_count'   => $c,
            'hierarchical' => $h,
        );

        if ($d) {
            $dropdown_id    = esc_attr(($first_dropdown) ? 'event_cat' : "{$this->id_base}-dropdown-{$this->number}");
            $first_dropdown = false;

            ?><label class="screen-reader-text" for="<?= $dropdown_id ?>">$title</label><?php

            $cat_args['show_option_none'] = __('Select Event Category');
            $cat_args['id']               = $dropdown_id;

            $taxonomy  = 'event_category';
            $tax_terms = get_terms($taxonomy);
            ?>
            <select id="<?= esc_html($dropdown_id) ?>" onchange="onEventCatChange()" title="Select Category">
                <option value="-1">Select Category</option>
                <?php
                $id = 0;
                foreach ($tax_terms as $tax_term) {
                    ?>
                    <option value="<?= esc_html($id) ?>"><?= esc_html($tax_term->name) ?></option><?php
                    $id++;
                }
                ?>
            </select>
            <script type='text/javascript'>
                /* <![CDATA[ */
                function onEventCatChange() {
                    var dropdown = document.getElementById("<?php echo esc_js($dropdown_id); ?>");
                    if (dropdown.options[dropdown.selectedIndex].value > 0) {
                        location.href = "<?= home_url(); ?>/event_category/" + dropdown.options[dropdown.selectedIndex].text;
                    }
                }
                /* ]]> */
            </script>
            <?php
        } else {
            $taxonomy  = 'event_category';
            $tax_terms = get_terms($taxonomy);
            ?>
            <ul>
                <?php
                foreach ($tax_terms as $tax_term) {
                    ?>
                    <li><a href="<?= esc_url(get_term_link($tax_term, $taxonomy)) ?>" title="View all posts in <?= esc_html($tax_term->name) ?>"><?= esc_html($tax_term->name) ?></a></li><?php
                }
                ?>
            </ul>
            <?php
        }

        echo $args['after_widget'];
    }
    #endregion

    #region Update
    public function update($new_instance, $old_instance)
    {
        $instance                 = $old_instance;
        $instance['title']        = SSV_General::sanitize($new_instance['title'], 'text');
        $instance['count']        = !empty($new_instance['count']) ? 1 : 0;
        $instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
        $instance['dropdown']     = !empty($new_instance['dropdown']) ? 1 : 0;

        return $instance;
    }
    #endregion

    #region Form
    public function form($instance)
    {
        //Defaults
        $instance     = wp_parse_args((array)$instance, array('title' => ''));
        $title        = SSV_General::sanitize($instance['title'], 'text');
        $count        = isset($instance['count']) ? (bool)$instance['count'] : false;
        $hierarchical = isset($instance['hierarchical']) ? (bool)$instance['hierarchical'] : false;
        $dropdown     = isset($instance['dropdown']) ? (bool)$instance['dropdown'] : false;
        ?>
        <p>
            <label for="<?= esc_html($this->get_field_id('title')) ?>">Title:</label>
            <input class="widefat" id="<?= esc_html($this->get_field_id('title')) ?>" name="<?= esc_html($this->get_field_name('title')) ?>" type="text" value="<?= esc_html($title) ?>"/>
        </p>

        <p>
            <input type="checkbox" class="checkbox" id="<?= esc_html($this->get_field_id('dropdown')) ?>" name="<?= esc_html($this->get_field_name('dropdown')) ?>"<?= checked($dropdown) ?> />
            <label for="<?= esc_html($this->get_field_id('dropdown')) ?>">Display as dropdown</label>
            <br/>
            <input type="checkbox" class="checkbox" id="<?= esc_html($this->get_field_id('count')) ?>" name="<?= esc_html($this->get_field_name('count')) ?>"<?php checked($count); ?> />
            <label for="<?= esc_html($this->get_field_id('count')) ?>">Show post counts</label>
            <br/>
            <input type="checkbox" class="checkbox" id="<?= esc_html($this->get_field_id('hierarchical')) ?>" name="<?= esc_html($this->get_field_name('hierarchical')) ?>"<?php checked($hierarchical); ?> />
            <label for="<?= esc_html($this->get_field_id('hierarchical')) ?>">Show hierarchy</label>
        </p>
        <?php
    }
    #endregion

}

add_action('widgets_init', create_function('', 'return register_widget("mp_ssv_events\widgets\ssv_event_category");'));
