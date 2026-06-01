<?php
banner_manager_load_css(array("assets/css/banner_manager_styles.css"));

$banner_ids = array();
foreach ($banners as $key => $banner) {
    array_push($banner_ids, $banner->id);
    ?>
    <div id="<?php echo "banner-$banner->id"; ?>" class="mb20 banner-container <?php echo ($key === 0) ? "" : "hide"; ?>">
        <img class="w-100" src="<?php echo banner_manager_get_banner_source_url($banner->banner); ?>" alt="..." />
        <?php
        if (count($banners) > 1) {
            echo js_anchor("<i class='icon-16' data-feather='arrow-right-circle'></i> " . app_lang("next"), array("class" => "btn btn-default banner-next-btn", "data-id" => $banner->id));
        }

        echo js_anchor("<i class='icon-16' data-feather='check-circle'></i> " . app_lang("banner_manager_got_it_thanks"), array("class" => "btn btn-success banner-confirm-btn spinning-btn", "data-id" => $banner->id));
        ?>
    </div>
    <?php
}
?>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        var bannerIds = <?php echo json_encode($banner_ids); ?>;

        //confirm this banner
        $("body").on("click", ".banner-confirm-btn", function () {
            var bannerId = $(this).attr("data-id");
            $(this).addClass("spinning");

            $.ajax({
                url: "<?php echo get_uri("banner_manager/mark_as_read"); ?>/" + bannerId,
                cache: false,
                type: 'POST',
                dataType: "json",
                success: function () {
                    showNextBanner(bannerId, true);
                }
            });
        });

        //show next banner
        $("body").on("click", ".banner-next-btn", function () {
            var bannerId = $(this).attr("data-id");
            showNextBanner(bannerId);
        });

        function showNextBanner(bannerId, removeThis) {
            var thisBannerIndex = bannerIds.indexOf(bannerId);

            //check if there has any item next on this item
            if ((thisBannerIndex + 1) === bannerIds.length) {
                //this is the last item
                //show first one
                $("#banner-" + bannerIds[0]).removeClass("hide");
            } else {
                //show next on this item
                $("#banner-" + bannerIds[thisBannerIndex + 1]).removeClass("hide");
            }

            if (removeThis) {
                $("#banner-" + bannerId).fadeOut(function () {
                    //remove this item
                    bannerIds.splice(thisBannerIndex, 1);

                    //hide next button if there has only one banner left
                    if (bannerIds.length === 1) {
                        $(".banner-next-btn").remove();
                    }

                    $(this).remove();
                });
            } else {
                //show next banner and just hide this
                $("#banner-" + bannerIds[thisBannerIndex]).addClass("hide");
            }
        }
    });
</script>
