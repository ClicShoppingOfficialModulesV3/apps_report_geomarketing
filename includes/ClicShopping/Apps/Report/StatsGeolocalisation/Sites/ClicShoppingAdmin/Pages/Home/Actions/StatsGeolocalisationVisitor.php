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

  class StatsGeolocalisationVisitor extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_StatsGeolocalisation = Registry::get('StatsGeolocalisation');

      $this->page->setFile('stats_geolocalisation_visitor.php');

      $CLICSHOPPING_StatsGeolocalisation->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }