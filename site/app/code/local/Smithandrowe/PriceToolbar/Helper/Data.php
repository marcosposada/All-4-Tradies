<?php
class Smithandrowe_PriceToolbar_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getPriceToolbar()
    {
        $is_price_inc_gst = Mage::getSingleton('core/session')->getPriceIncGST();
        $inc_gst_class = $ex_gst_class = NULL;
        $inc_gst_check = $ex_gst_check = NULL;
        if ($is_price_inc_gst == 1) {
            $inc_gst_class = ' active';
            $inc_gst_check = '<i class="fa fa-check"></i>';
        } else {
            $ex_gst_class = ' active';
            $ex_gst_check = '<i class="fa fa-check"></i>';
        }
        $output = '<div class="price-toolbar"><span>Show Prices</span><a href="/set-price/?gst=on" class="inc-gst' . $inc_gst_class . '">INC GST' . $inc_gst_check . '</a><a href="/set-price/?gst=off" class="ex-gst'. $ex_gst_class . '">EX GST' . $ex_gst_check . '</a></div>';

        return $output;
    }

}
?>
