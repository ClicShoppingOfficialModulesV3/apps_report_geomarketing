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

  namespace ClicShopping\OM\Module\Hooks\Shop\Footer;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Report\StatsGeolocalisation\Classes\Shop\GeolocalisationShop;

  class FooterCallGeomarketing
  {
    /**
     * @return bool
     */
    private static function getTracking() :bool
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->prepare('select no_ip_address
                                           from :table_customers_gdpr
                                           where customers_id = :customers_id
                                         ');
      $Qcheck->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qcheck->execute();

      if (empty($Qcheck->value('no_ip_address'))) {
        return true;
      } else {
        return false;
      }
    }

    public function execute()
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if (defined('CLICSHOPPING_APP_STATS_GEOLOCALISATION_SG_STATUS') && CLICSHOPPING_APP_STATS_GEOLOCALISATION_SG_STATUS == 'True' && CLICSHOPPING_APP_STATS_GEOLOCALISATION_SG_GRPD == 'True') {
        Registry::set('GeolocalisationShop', new GeolocalisationShop());
        $CLICSHOPPING_Geomarketing = Registry::get('GeolocalisationShop');

        if (static::getTracking() === true && $CLICSHOPPING_Customer->isLoggedOn()) {
          $CLICSHOPPING_Geomarketing->saveData();
        } elseif(static::getTracking() === true && !$CLICSHOPPING_Customer->isLoggedOn()) {
          $CLICSHOPPING_Geomarketing->saveData();
        }
      }
    }
  }