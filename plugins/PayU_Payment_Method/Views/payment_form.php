<?php
echo form_open(get_uri("payu_payment_method/initiate_payment_process"), array("id" => "payu-checkout-form", "class" => "float-start", "role" => "form"));
?>
<input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>" />
<input type="hidden" name="payment_amount" value="<?php echo to_decimal_format($balance_due); ?>"  id="payu-payment-amount-field" />
<input type="hidden" name="verification_code" value="<?php echo isset($verification_code) ? $verification_code : ""; ?>" />
<input type="hidden" name="contact_user_id" value="<?php echo isset($contact_user_id) ? $contact_user_id : ""; ?>" />

<input type="hidden" name="currency" value="<?php echo $currency; ?>" />
<input type="hidden" name="balance_due" value="<?php echo $balance_due; ?>" />
<input type="hidden" name="client_id" value="<?php echo $invoice_info->client_id; ?>" />
<input type="hidden" name="payment_method_id" value="<?php echo get_array_value($payment_method, "id"); ?>" />
<input type="hidden" name="description" value="<?php echo app_lang("pay_invoice"); ?>: (<?php echo to_currency($balance_due, $currency . " "); ?>)" id="payu_description" />

<button type="button" id="payu-payment-button" class="btn btn-primary mr15 spinning-btn"><?php echo get_array_value($payment_method, "pay_button_text"); ?></button>
<?php echo form_close(); ?>

<script type="text/javascript">
    "use strict";

    $(document).ready(function () {
        var currency = "<?php echo $currency . ' '; ?>",
                payInvoiceText = "<?php echo app_lang("pay_invoice"); ?>";
        var $button = $("#payu-payment-button");

        $button.on('click', function (event) {

            //show an error message if user attempt to pay more than the invoice due and exit
<?php if (get_setting("allow_partial_invoice_payment_from_clients")) { ?>
                if (unformatCurrency($("#payment-amount").val()) > "<?php echo $balance_due; ?>") {
                    appAlert.error("<?php echo app_lang("invoice_over_payment_error_message"); ?>");
                    return false;
                }
<?php } ?>

            $button.addClass("spinning");

            $(this).trigger("submit");
        });

        var minimumPaymentAmount = "<?php echo get_array_value($payment_method, 'minimum_payment_amount'); ?>" * 1;
        if (!minimumPaymentAmount || isNaN(minimumPaymentAmount)) {
            minimumPaymentAmount = 1;
        }

        $("#payment-amount").on("change", function () {
            //changed the amount. update the description on payu payment form
            var value = $(this).val();
            $("#payu_description").val(payInvoiceText + " (" + toCurrency(unformatCurrency(value), currency) + ")");

            //change payu payment amount field value as inputed/ don't use unformatCurrency we'll do it in controller
            $("#payu-payment-amount-field").val(value);

            //check minimum payment amount and show/hide payment button
            if (value < minimumPaymentAmount) {
                $("#payu-payment-button").hide();
            } else {
                $("#payu-payment-button").show();
            }

        });

        $("#payu-checkout-form").appForm({
            isModal: false,
            showLoader: false,
            onSuccess: function (response) {
                if (response.success) {
                    window.location.href = response.redirect_to;
                } else {
                    appAlert.error(response.message);
                }
            }
        });

    });
</script>