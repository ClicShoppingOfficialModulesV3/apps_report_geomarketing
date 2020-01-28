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

  use ClicShopping\OM\HTML;

  class grpd extends \ClicShopping\Apps\Report\StatsGeolocalisation\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = 'True';
    public $sort_order = 20;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_modules_stats_geolocalisation_grpd_title');
      $this->description = $this->app->getDef('cfg_modules_stats_geolocalisation_grpd_description');
    }

    public function getInputField()
    {
      $value = $this->getInputValue();

      $input = HTML::radioField($this->key, 'True', $value, 'id="' . $this->key . '1" autocomplete="off"') . $this->app->getDef('cfg_modules_stats_geolocalisation_grpd_true') . ' ';
      $input .= HTML::radioField($this->key, 'False', $value, 'id="' . $this->key . '2" autocomplete="off"') . $this->app->getDef('cfg_modules_stats_geolocalisation_grpd_false');

      return $input;
    }
  }