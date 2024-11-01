<?php
namespace mp_ssv_events;
use WP_Query;

if (!defined('ABSPATH')) {
    exit;
}
#region setup variables
$args = array(
    'posts_per_page' => 10,
    'paged'          => get_query_var('paged'),
    'post_type'      => 'events',
    'meta_key'       => 'start',
    'meta_value'     => date("Y-m-d", time()),
    'orderby'        => 'meta_value',
    'groupby'        => 'meta_value',
);

$args['meta_compare'] = '>=';
$args['order']        = 'ASC';
$upcomingEvents       = new WP_Query($args);

$args['posts_per_page'] = 10 - $upcomingEvents->post_count;
$args['meta_compare']   = '<';
$args['order']          = 'DESC';
$pastEvents             = new WP_Query($args);
#endregion

#region base layout
get_header();
?>
    <div id="page" class="container <?= is_admin_bar_showing() ? 'wpadminbar' : '' ?>">
        <div class="row">
            <div class="col s12 <?= is_dynamic_sidebar() ? 'm8 l9 xxl10' : '' ?>">
                <div id="primary" class="content-area">
                    <main id="main" class="site-main" role="main">
                        <?php mp_ssv_events_content_theme_default($upcomingEvents, $pastEvents); ?>
                    </main>
                </div>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
<?php
get_footer();
#endregion

/**
 * This function prints the default event preview lists (only for themes with support for "materialize").
 *
 * @param WP_Query $upcomingEvents
 * @param WP_Query $pastEvents
 */
function mp_ssv_events_content_theme_default($upcomingEvents, $pastEvents)
{
    $hasUpcomingEvents = $upcomingEvents->have_posts();
    $hasPastEvents     = $pastEvents->have_posts();
    if ($hasUpcomingEvents || $hasPastEvents) {
        if ($hasUpcomingEvents) {
            ?>
            <header class="full-width-entry-header" style="margin: 15px 0;">
                <div class="parallax-container primary" style="height: 150px;">
                    <div class="shade darken-1 valign-wrapper" style="height: 100%">
                        <h1 class="entry-title center-align white-text valign" style="margin-top: 0; padding-top: 30px">Upcoming</h1>
                    </div>
                </div>
            </header>
            <?php
            while ($upcomingEvents->have_posts()) {
                $upcomingEvents->the_post();
                require 'event-views/archive-preview-material.php';
            }
        }
        if ($hasPastEvents) {
            ?>
            <header class="full-width-entry-header" style="margin: 15px 0;">
                <div class="parallax-container primary" style="height: 150px;">
                    <div class="shade darken-1 valign-wrapper" style="height: 100%">
                        <h1 class="entry-title center-align white-text valign" style="margin-top: 0; padding-top: 30px">Past</h1>
                    </div>
                </div>
            </header>
            <?php
            while ($pastEvents->have_posts()) {
                $pastEvents->the_post();
                require 'event-views/archive-preview-material.php';
            }
        }
        echo mp_ssv_get_pagination();
    } else {
        get_template_part('template-parts/content', 'none');
    }
}
