<?php
/*
Template Name: 金句页
*/
get_header();
?>
<style>
.jitang-page{min-height:50vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:60px 20px}
.jitang-text{max-width:720px;font-size:26px;line-height:1.9;font-weight:500}
.jitang-from{margin-top:18px;color:var(--muted-color);font-size:15px}
.jitang-refresh{margin-top:34px}
</style>
<div class="ioui-content switch-container container sidebar_no">
    <div class="ioui-main">
        <div class="content-wrap">
            <div class="jitang-page">
                <div class="jitang-text" id="jitang-text">人生苦短，我用Python。</div>
                <div class="jitang-from" id="jitang-from"></div>
                <a href="javascript:;" class="btn vc-l-theme btn-outline jitang-refresh" id="jitang-refresh"><i class="iconfont icon-refresh mr-1"></i>换一句</a>
            </div>
        </div>
    </div>
</div>
<script>
(function() {
    function loadJitang() {
        var $t = jQuery('#jitang-text').text('加载中...');
        jQuery.ajax({
            url: 'https://v1.hitokoto.cn/?c=d&c=i&c=k&encode=json',
            dataType: 'json',
            timeout: 8000,
            success: function(res) {
                if (res && res.hitokoto) {
                    jQuery('#jitang-text').text(res.hitokoto);
                    jQuery('#jitang-from').text(res.from ? '—— ' + res.from : '');
                }
            },
            error: function() {
                jQuery('#jitang-text').text('人生苦短，我用Python。');
            }
        });
    }
    jQuery('#jitang-refresh').on('click', loadJitang);
    loadJitang();
})();
</script>
<?php get_footer(); ?>
