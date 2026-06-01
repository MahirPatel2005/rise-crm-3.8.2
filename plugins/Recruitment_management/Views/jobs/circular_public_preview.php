<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo view('includes/head'); ?>
    </head>
    <body>

        <style>
            .card {
                transition: all 0s !important;
            }

            .mt4{
                margin-top: 4px;
            }
        </style>

        <div id="proposal-preview-scrollbar">

            <div id="page-content" class="page-wrapper clearfix">
                <?php
                load_css(array(
                    "assets/css/invoice.css",
                ));

                load_js(array(
                    "assets/js/signature/signature_pad.min.js",
                ));

                recruitment_load_css(
                        array(PLUGIN_URL_PATH . "Recruitment_management/assets/css/recruitment-style.css")
                );
                ?>

                <div class="circular-preview">
                    <div class = "card  p15 no-border">
                        <div class="clearfix">
                            <img class="dashboard-image float-start" src="<?php echo get_logo_url(); ?>" />
                        </div>
                    </div>

                    <div class="invoice-preview-container bg-white">
                        <?php
                        echo $circular_preview;
                        ?>
                    </div>

                </div>
            </div>

            <?php echo view('modal/index'); ?>

        </div>

        <script>
            "use strict";

            $(document).ready(function () {
                initScrollbar('#proposal-preview-scrollbar', {
                    setHeight: $(window).height()
                });

                $("#custom-theme-color").remove();
            });
        </script>
    </body>
</html>