<?php
if (post_password_required()) {
    return;
}
$post_id = get_the_ID();
$commenter = wp_get_current_commenter();
$user = wp_get_current_user();
$comments_number = (int) get_comments_number($post_id);
$default_avatar = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 80 80"><rect width="80" height="80" rx="40" fill="#e9edf3"/><circle cx="40" cy="31" r="14" fill="#b7c0d2"/><path d="M14 70c3-15 14-22 26-22s23 7 26 22z" fill="#b7c0d2"/></svg>');
$comment_tree = function_exists('theme_render_comment_tree') ? theme_render_comment_tree($post_id) : '';
?>
<div id="comments" class="comments">
    <h2 id="comments-list-title" class="comments-title text-lg mx-1 my-4">
        <i class="iconfont icon-comment"></i>
        <span class="noticom">
            <?php if ($comments_number === 0) : ?>
            <a href="#respond" class="comments-title">暂无评论</a>
            <?php else : ?>
            <span class="comments-title"><?php echo number_format_i18n($comments_number); ?> 条评论</span>
            <?php endif; ?>
        </span>
    </h2>
    <div class="card">
        <div class="card-body">
            <div id="comment-list-box">
                <?php echo $comment_tree; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>

            <?php if ($comments_number === 0) : ?>
            <div class="col-1a-i nothing-box nothing-type-none" id="comment-nothing">
                <div class="nothing">
                    <svg class="nothing-svg" viewBox="0 0 220 170" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="110" cy="148" rx="82" ry="10" fill="#eef1f6"/>
                        <path d="M44 78h104l22 18v34a10 10 0 0 1-10 10H54a10 10 0 0 1-10-10V88z" fill="#ffd9a8"/>
                        <path d="M148 78l22 18h-16a6 6 0 0 1-6-6V78z" fill="#f5b96e"/>
                        <path d="M58 104h76M58 120h52" stroke="#e09b4a" stroke-width="5" stroke-linecap="round"/>
                        <circle cx="166" cy="64" r="27" fill="#fff" stroke="#d9deea" stroke-width="3"/>
                        <circle cx="157" cy="59" r="3.4" fill="#9aa6bf"/><circle cx="175" cy="59" r="3.4" fill="#9aa6bf"/>
                        <path d="M157 69q9 8 18 0" stroke="#9aa6bf" stroke-width="3" fill="none" stroke-linecap="round"/>
                        <line x1="166" y1="37" x2="166" y2="27" stroke="#7c8db5" stroke-width="3" stroke-linecap="round"/>
                        <line x1="139" y1="64" x2="131" y2="64" stroke="#7c8db5" stroke-width="3" stroke-linecap="round"/>
                        <line x1="193" y1="64" x2="201" y2="64" stroke="#7c8db5" stroke-width="3" stroke-linecap="round"/>
                        <text x="96" y="122" font-size="36" font-weight="bold" fill="#e8893a">?</text>
                    </svg>
                    <div class="nothing-msg text-sm text-muted">暂无评论，快来发表第一条评论吧</div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (comments_open($post_id)) : ?>
            <div id="respond_box">
                <div id="respond" class="comment-respond">
                    <form id="commentform" class="text-sm mb-4" action="<?php echo esc_url(site_url('/wp-comments-post.php')); ?>" method="post">
                        <div class="avatar-box d-flex align-items-center flex-fill mb-2">
                            <div class="avatar-img">
                                <?php if (is_user_logged_in()) : ?>
                                <?php echo get_avatar($user->ID, 44, '', $user->display_name, array('class' => 'avatar rounded-circle')); ?>
                                <?php else : ?>
                                <img class="avatar rounded-circle" src="<?php echo esc_url($default_avatar); ?>" alt="默认头像" width="44" height="44">
                                <?php endif; ?>
                            </div>
                            <?php if (is_user_logged_in()) : ?>
                            <span class="text-muted text-xs ml-2">当前登录：<?php echo esc_html($user->display_name); ?>（<a href="<?php echo esc_url(wp_logout_url(get_permalink($post_id))); ?>">退出</a>）</span>
                            <?php endif; ?>
                            <span class="reply-tip text-xs ml-2" style="display:none;color:var(--theme-color);"></span>
                        </div>
                        <div class="comment-textarea mb-3">
                            <textarea name="comment" id="comment" class="form-control" placeholder="输入评论内容..." tabindex="4" cols="50" rows="3" required></textarea>
                        </div>
                        <?php if (!is_user_logged_in()) : ?>
                        <div id="comment-author-info" class="row row-sm">
                            <div class="col-12 col-md-6 mb-3">
                                <input type="text" name="author" id="author" class="form-control" value="<?php echo esc_attr($commenter['comment_author'] ?? ''); ?>" size="22" placeholder="昵称" tabindex="2" required>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <input type="email" name="email" id="email" class="form-control" value="<?php echo esc_attr($commenter['comment_author_email'] ?? ''); ?>" size="22" placeholder="邮箱（仅用于显示头像）" tabindex="3" required>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="com-footer d-flex justify-content-end flex-wrap">
                            <a rel="nofollow" id="cancel-comment-reply-link" style="display:none;" href="javascript:;" class="btn vc-l-gray mx-2">取消回复</a>
                            <button class="btn btn-hover-dark btn-shadow vc-theme ml-2" type="submit" id="submit" name="submit">发表评论</button>
                            <input type="hidden" name="action" value="theme_ajax_comment">
                            <input type="hidden" name="_wpnonce" value="<?php echo esc_attr(wp_create_nonce('theme_comment_nonce')); ?>">
                            <input type="hidden" name="comment_post_ID" value="<?php echo (int) $post_id; ?>" id="comment_post_ID">
                            <input type="hidden" name="comment_parent" id="comment_parent" value="0">
                            <input type="hidden" name="_wp_http_referer" value="<?php echo esc_url(wp_unslash($_SERVER['REQUEST_URI'] ?? '/')); ?>">
                        </div>
                    </form>
                    <div id="loading-comments" style="display:none"><span></span></div>
                    <div class="clear"></div>
                </div>
            </div>
            <?php elseif ($comments_number > 0) : ?>
            <p class="no-comments text-muted text-center py-3">评论已关闭</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function () {
    var form = document.getElementById('commentform');
    if (!form) return;
    var $ = window.jQuery;
    var listBox = document.getElementById('comment-list-box');
    var nothing = document.getElementById('comment-nothing');
    var titleBox = document.querySelector('#comments-list-title .noticom');
    var parentInput = document.getElementById('comment_parent');
    var cancelLink = document.getElementById('cancel-comment-reply-link');
    var replyTip = form.querySelector('.reply-tip');
    var loading = document.getElementById('loading-comments');
    var submitBtn = document.getElementById('submit');

    function updateCount(n) {
        if (!titleBox) return;
        titleBox.innerHTML = n > 0
            ? '<span class="comments-title">' + n + ' 条评论</span>'
            : '<a href="#respond" class="comments-title">暂无评论</a>';
    }

    // 回复：把父评论 ID 写入隐藏域并滚动到输入框
    document.addEventListener('click', function (e) {
        var link = e.target.closest('.comment-reply-link');
        if (!link || !document.getElementById('comments').contains(link)) return;
        e.preventDefault();
        parentInput.value = link.getAttribute('data-id') || '0';
        if (replyTip) {
            replyTip.textContent = '回复 @' + (link.getAttribute('data-name') || '').toString();
            replyTip.style.display = '';
        }
        if (cancelLink) cancelLink.style.display = '';
        var ta = document.getElementById('comment');
        if (ta) {
            ta.focus();
            ta.scrollIntoView({behavior: 'smooth', block: 'center'});
        }
    });

    function resetReply() {
        parentInput.value = '0';
        if (cancelLink) cancelLink.style.display = 'none';
        if (replyTip) {
            replyTip.textContent = '';
            replyTip.style.display = 'none';
        }
    }
    if (cancelLink) {
        cancelLink.addEventListener('click', function (e) {
            e.preventDefault();
            resetReply();
        });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var content = (form.comment.value || '').trim();
        if (content.length < 2) {
            window.showAlert ? showAlert({status: 4, msg: '请输入至少 2 个字的评论内容'}) : alert('请输入至少 2 个字的评论内容');
            return;
        }
        if (!form.author || !form.email || (form.author.value.trim() && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email.value))) {
            // 已登录或游客昵称邮箱完整
        } else {
            window.showAlert ? showAlert({status: 4, msg: '请填写昵称和正确的邮箱地址'}) : alert('请填写昵称和正确的邮箱地址');
            return;
        }

        var data = $(form).serialize();
        submitBtn.disabled = true;
        if (loading) loading.style.display = 'block';

        $.post(IO.ajaxurl, data, function (res) {
            // WP 的 wp_send_json_error 返回 HTTP 200，需在此判断业务失败
            if (res && res.success === false) {
                var emsg = (res.data && res.data.msg) ? res.data.msg : '评论发表失败，请稍后重试';
                if (window.showAlert) showAlert({status: 4, msg: emsg}); else alert(emsg);
                return;
            }
            var d = res && res.data ? res.data : res;
            if (listBox && d.html) listBox.innerHTML = d.html;
            if (nothing && d.count > 0) nothing.style.display = 'none';
            if (nothing && d.count === 0) nothing.style.display = '';
            updateCount(d.count || 0);
            form.reset();
            resetReply();
            if (window.showAlert) showAlert({status: 1, msg: '评论发表成功'});
            if (d.comment_id) {
                var fresh = document.getElementById('comment-' + d.comment_id);
                if (fresh) {
                    fresh.classList.add('comment-flash');
                    fresh.scrollIntoView({behavior: 'smooth', block: 'center'});
                }
            }
        }).fail(function (xhr) {
            var msg = '评论发表失败，请稍后重试';
            try {
                var rj = JSON.parse(xhr.responseText);
                if (rj && rj.data && rj.data.msg) msg = rj.data.msg;
            } catch (err) {}
            if (window.showAlert) showAlert({status: 4, msg: msg}); else alert(msg);
        }).always(function () {
            submitBtn.disabled = false;
            if (loading) loading.style.display = 'none';
        });
    });
})();
</script>

<style>
#comments .avatar-box .avatar{width:44px;height:44px;object-fit:cover}
#comments .comment-textarea textarea{resize:vertical}
#comments .nothing-box{text-align:center;padding:34px 0 10px}
#comments .nothing-svg{max-width:220px;width:62%;height:auto}
#comments .comment-list{list-style:none;margin:0;padding:0}
#comments .comment-list .children{list-style:none;margin:0 0 6px;padding-left:52px}
#comments .comment-list .comment-body{padding:10px 0;border-bottom:1px solid rgba(136,136,136,.12)}
#comments .comment-list > li:last-child > .comment-body{border-bottom:none}
#comments .comment-avatar img{width:44px;height:44px;object-fit:cover}
#comments .comment-head .comment-author{font-size:.875rem}
#comments .comment-reply-link{font-weight:400}
#comments .comment-text{color:var(--body-color);line-height:1.7;word-break:break-word}
#comments #loading-comments{text-align:center;padding:10px 0}
#comments #loading-comments span{display:inline-block;width:22px;height:22px;border:2px solid var(--theme-color);border-top-color:transparent;border-radius:50%;animation:theme-comment-spin .7s linear infinite}
@keyframes theme-comment-spin{to{transform:rotate(360deg)}}
#comments .comment-flash{animation:theme-comment-flash 1.6s ease}
@keyframes theme-comment-flash{0%{background:rgba(245,108,108,.18)}100%{background:transparent}}
</style>
