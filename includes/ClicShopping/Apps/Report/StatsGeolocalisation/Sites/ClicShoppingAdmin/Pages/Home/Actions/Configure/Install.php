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

  namespace ClicShopping\Apps\Report\StatsGeolocalisation\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  use ClicShopping\OM\Cache;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_StatsGeolocalisation = Registry::get('StatsGeolocalisation');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_StatsGeolocalisation->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('StatsGeolocalisationAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_StatsGeolocalisation->getDef('alert_module_install_success'), 'success', 'StatsGeolocalisation');

      $CLICSHOPPING_StatsGeolocalisation->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_StatsGeolocalisation = Registry::get('StatsGeolocalisation');
      $CLICSHOPPING_Language = Registry::get('Language');

      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_report_stats_geolocalisation']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 5,
          'link' => '',
          'image' => '',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_report_stats_geolocalisation'
        ];

        $insert_sql_data = ['parent_id' => 7];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_StatsGeolocalisation->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
        }



        $QParent = $CLICSHOPPING_Db->prepare('select id
                                               from :table_administrator_menu
                                               where app_code = :app_code
                                              ');
        $QParent->bindValue(':app_code', 'app_report_stats_geolocalisation');
        $QParent->execute();

//*************************************
// Visitor
//*************************************
        $sql_data_array = ['sort_order' => 2,
          'link' => 'index.php?A&Report\StatsGeolocalisation&StatsGeolocalisationVisitor',
          'image' => '',
          'b2b_menu' => 0,
          'access' => 1,
          'app_code' => 'app_report_stats_geolocalisation_visitor'
        ];

        $insert_sql_data = ['parent_id' => $QParent->valueInt('id')];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_StatsGeolocalisation->getDef('title_menu_order_visitor')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
        }

//*************************************
// Order
//*************************************
        $sql_data_array = ['sort_order' => 2,
          'link' => 'index.php?A&Report\StatsGeolocalisation&StatsGeolocalisationOrder',
          'image' => '',
          'b2b_menu' => 0,
          'access' => 1,
          'app_code' => 'app_report_stats_geolocalisation_order'
        ];

        $insert_sql_data = ['parent_id' => $QParent->valueInt('id')];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_StatsGeolocalisation->getDef('title_menu_order_map')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
        }

        Cache::clear('menu-administrator');
      }
    }

    public static function installDb() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_info_customer_tracking"');

      if ($Qcheck->fetch() === false) {
        $CLICSHOPPING_Db->installNewDb('info_customer_tracking');
      }
    }
  }
