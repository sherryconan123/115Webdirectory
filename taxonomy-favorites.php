<?php get_header();

$term = get_queried_object();
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'date';

$order_args = array();
switch ($orderby) {
    case 'modified':
        $order_args['orderby'] = 'modified';
        $order_args['order'] = 'DESC';
        break;
    case 'views':
        $order_args['orderby'] = 'meta_value_num';
        $order_args['meta_key'] = 'site_views';
        $order_args['order'] = 'DESC';
        break;
    case 'like':
        $order_args['orderby'] = 'meta_value_num';
        $order_args['meta_key'] = 'site_likes';
        $order_args['order'] = 'DESC';
        break;
    default:
        $order_args['orderby'] = 'date';
        $order_args['order'] = 'DESC';
        break;
}

$all_terms = get_terms(array(
    'taxonomy' => 'favorites',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 20,
));

$term_icon = theme_get_category_icon($term->slug);
?>

<div class="ioui-content switch-container container">
    <div class="ioui-main">
        <div class="content-wrap">
            <div class="content-layout">
                <?php theme_breadcrumb(); ?>

                <div class="content-card">
                    <div class="d-flex flex-fill align-items-center mb-3">
                        <h4 class="tab-title text-gray text-lg font-bold m-0">
                            <i class="<?php echo $term_icon; ?> icon-fw mr-2"></i><?php single_term_title(); ?>
                        </h4>
                        <div class="flex-fill"></div>
                        <div class="meta-ico text-muted text-xs">
                            <span class="meta-view"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>共 <?php echo $wp_query->found_posts; ?> 条内容</span>
                        </div>
                    </div>

                    <?php if (!empty($all_terms) && !is_wp_error($all_terms)) : ?>
                    <div class="cat-nav-tabs no-scrollbar overflow-x-auto mb-3">
                        <?php foreach ($all_terms as $t) :
                            $icon = theme_get_category_icon($t->slug);
                            $active = ($t->term_id == $term->term_id) ? 'active' : '';
                        ?>
                        <a href="<?php echo esc_url(get_term_link($t)); ?>" class="cat-nav-tab <?php echo $active; ?>">
                            <i class="<?php echo $icon; ?> icon-fw"></i>
                            <span><?php echo $t->name; ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex align-items-center mb-3">
                        <span class="text-muted text-xs mr-2">排序</span>
                        <div class="list-selects no-scrollbar">
                            <a class="list-select <?php echo $orderby == 'date' ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg('orderby', 'date', get_term_link($term))); ?>">发布</a>
                            <a class="list-select <?php echo $orderby == 'modified' ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg('orderby', 'modified', get_term_link($term))); ?>">更新</a>
                            <a class="list-select <?php echo $orderby == 'views' ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg('orderby', 'views', get_term_link($term))); ?>">浏览</a>
                            <a class="list-select <?php echo $orderby == 'like' ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg('orderby', 'like', get_term_link($term))); ?>">点赞</a>
                        </div>
                    </div>

                    <?php
                    global $wp_query;
                    if ($orderby != 'date') {
                        $args = array_merge($wp_query->query_vars, $order_args);
                        query_posts($args);
                    }
                    ?>

                    <?php if (have_posts()) : ?>
                    <div class="posts-row row-col-2a row-col-sm-2a row-col-md-3a row-col-lg-4a row-col-xl-5a row-col-xxl-6a">
                        <?php
                        $item_idx = 0;
                        while (have_posts()) : the_post();
                            theme_render_collection_item(get_post_type(get_the_ID()), $item_idx++);
                        endwhile;
                        ?>
                    </div>
                    <?php theme_pagination(); ?>
                    <?php else : ?>
                        <div class="text-center py-10 text-muted">暂无相关内容</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
