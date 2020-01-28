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

  namespace ClicShopping\Apps\Report\StatsGeolocalisation\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Report\StatsGeolocalisation\StatsGeolocalisation;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public $app;

    protected function init()
    {
      $CLICSHOPPING_StatsGeolocalisation = new StatsGeolocalisation();
      Registry::set('StatsGeolocalisation', $CLICSHOPPING_StatsGeolocalisation);

     $CLICSHOPPING_StatsGeolocalisation = Registry::get('StatsGeolocalisation');

      $this->app = $CLICSHOPPING_StatsGeolocalisation;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
