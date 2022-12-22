<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Report\StatsGeolocalisation\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class RemoveIP extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_StatsGeolocalisation = Registry::get('StatsGeolocalisation');

      if (isset($_GET['RemoveIP']) &&!empty($_POST['remove_ip'])) {
        $ip = HTML::sanitize($_POST['remove_ip']);
        $CLICSHOPPING_StatsGeolocalisation->db->delete('info_customer_tracking', ['ip_address' => $ip]);
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_StatsGeolocalisation->getDef('alert_module_delete_success'), 'success');
      }

      $CLICSHOPPING_StatsGeolocalisation->redirect('StatsGeolocalisationVisitor');
    }
  }