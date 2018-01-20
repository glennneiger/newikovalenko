<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MeprPowerPressCtrl extends MeprBaseCtrl {
  public function load_hooks() {
    add_filter('powerpress_admin_capabilities',array($this,'powerpress_caps'));
  }

  public function powerpress_caps($caps) {
    $products = MeprCptModel::all('MeprProduct');

    $caps['mepr-active'] = __('MemberPress Active Member', 'memberpress');

    // Add Dynamic MemberPress capabilities into the mix
    foreach($products as $product) {
      $caps["mepr-membership-auth-{$product->ID}"] = sprintf(__('MemberPress: %s', 'memberpress'), $product->post_title);
    }

    return $caps;
  }

} //End class

