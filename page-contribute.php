<?php
/*
Template Name: 投稿收录页
*/
get_header();
$is_logged_in = is_user_logged_in();
$create_items = array(
    array('title' => '新建文章', 'icon' => 'icon-article', 'color' => '#409eff', 'url' => admin_url('post-new.php')),
    array('title' => '新建网址', 'icon' => 'icon-sites', 'color' => '#67c23a', 'url' => admin_url('post-new.php?post_type=sites')),
    array('title' => '新建软件', 'icon' => 'icon-app', 'color' => '#e6a23c', 'url' => admin_url('post-new.php?post_type=app')),
    array('title' => '新建书籍', 'icon' => 'icon-book', 'color' => '#9b59b6', 'url' => admin_url('post-new.php?post_type=book')),
);
?>
<style>
.contribute-hero{text-align:center;padding:40px 16px 26px}
.contribute-hero h1{font-size:26px;margin-bottom:10px}
.contribute-hero p{color:var(--muted-color);margin:0}
.contribute-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;max-width:720px;margin:24px auto 0}
@media(min-width:768px){.contribute-grid{grid-template-columns:repeat(4,1fr)}}
.contribute-card{display:flex;flex-direction:column;align-items:center;padding:28px 12px;border-radius:12px;background:var(--card-bg,#fff);border:1px solid var(--border-color,rgba(116,116,116,.12));color:inherit;transition:all .2s}
.contribute-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.08)}
.contribute-card .ct-ico{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;margin-bottom:12px}
.contribute-card .ct-name{font-size:15px}
.contribute-login{max-width:720px;margin:24px auto 0;padding:34px 20px;text-align:center}
.contribute-login .login-btns{margin-top:16px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
.contribute-notice{max-width:720px;margin:22px auto 0}
.contribute-notice .card-body{padding:20px 24px}
.contribute-notice h3{font-size:16px;margin-bottom:10px}
.contribute-notice ul{padding-left:20px;color:var(--muted-color);line-height:2}
</style>
<div class="ioui-content switch-container container sidebar_no py-4">
    <div class="ioui-main">
        <div class="content-wrap">
            <div class="contribute-hero">
                <h1>投稿收录</h1>
                <p>把你的发现记录下来，让每一份灵感与收获都成为你成长的基石。</p>
            </div>
            <?php if ($is_logged_in) : ?>
            <div class="contribute-grid">
                <?php foreach ($create_items as $item) : ?>
                <a class="contribute-card" href="<?php echo esc_url($item['url']); ?>" target="_blank" rel="nofollow">
                    <span class="ct-ico" style="background:<?php echo esc_attr($item['color']); ?>"><i class="iconfont <?php echo esc_attr($item['icon']); ?>"></i></span>
                    <span class="ct-name"><?php echo esc_html($item['title']); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else : ?>
            <div class="card contribute-login">
                <h3 class="text-md">需要登录才能访问！</h3>
                <p class="text-muted text-sm mt-2">登录后即可投稿网址、文章、软件与书籍，开通会员可尊享更多权益！</p>
                <div class="login-btns">
                    <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" class="btn vc-l-blue px-4">登录</a>
                    <a href="<?php echo esc_url(wp_registration_url()); ?>" class="btn vc-l-green px-4">注册</a>
                    <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="btn vc-l-yellow px-4">找回密码</a>
                </div>
            </div>
            <?php endif; ?>
            <div class="card contribute-notice">
                <div class="card-body">
                    <h3><i class="iconfont icon-notice mr-2"></i>投稿须知</h3>
                    <?php
                    if (have_posts()) :
                        while (have_posts()) : the_post();
                            the_content();
                        endwhile;
                    endif;
                    ?>
                    <ul>
                        <li>提交内容须为互联网公开、合法合规的优质资源；</li>
                        <li>请填写准确的名称、网址与简介，确保资源可正常访问；</li>
                        <li>严禁提交涉黄涉赌涉毒、侵权盗版及其他违法内容；</li>
                        <li>审核通过后内容将在对应分类展示，未通过不再另行通知。</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>
