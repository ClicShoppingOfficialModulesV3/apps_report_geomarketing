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

  namespace ClicShopping\Apps\Report\StatsGeolocalisation\Module\ClicShoppingAdmin\Config\SG\Params;

  class exclude_ip extends \ClicShopping\Apps\Report\StatsGeolocalisation\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {

    public $default = '91.242.162.8, 66.249.66.158, 5.9.110.227';
    public $sort_order = 30;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_modules_stats_geolocalisation_exclude_ip_title');
      $this->description = $this->app->getDef('cfg_modules_stats_geolocalisation_exclude_ip_description');
    }
  }
