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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Report\StatsGeolocalisation\Classes\ClicShoppingAdmin\GeolocalisationAdmin;

  use ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin\GroupsB2BAdmin;

  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
  $CLICSHOPPING_StatsGeolocalisation = Registry::get('StatsGeolocalisation');
  $CLICSHOPPING_Language = Registry::get('Language');

  Registry::set('GeolocalisationAdmin', new GeolocalisationAdmin());
  $CLICSHOPPING_GeolocalisationAdmin = Registry::get('GeolocalisationAdmin');

  $min_zoom = 2.5;
  $limit = '';
  $view = 0;
  $customers_group_id = 99;
  $language = 0;

  if (isset($_POST['limit'])) {
    $limit = HTML::sanitize($_POST['limit']);
  }

  if (isset($_POST['min_zoom'])) {
    $min_zoom = HTML::sanitize($_POST['min_zoom']);
  }

  if (isset($_POST['view'])) {
    $view = HTML::sanitize($_POST['view']);
  }

  if (isset($_POST['customers_group_id'])) {
    $customers_group_id = HTML::sanitize($_POST['customers_group_id']);
  }

  if (isset( $_POST['language'])) {
    $language = HTML::sanitize( $_POST['language']);
  }
?>
<!--https://github.com/asmaloney/Leaflet_Example-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.6.0/leaflet.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.6.0/leaflet.js"></script>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading">
            <?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/icon_search.png', $CLICSHOPPING_StatsGeolocalisation->getDef('heading_title_order'), '40', '40'); ?>
          </span>
          <span class="col-md-3 pageHeading">
            <?php echo '&nbsp;' . $CLICSHOPPING_StatsGeolocalisation->getDef('heading_title_order'); ?>
          </span>

          <span class="col-md-8 text-end">
            <span><?php echo HTML::button($CLICSHOPPING_StatsGeolocalisation->getDef('button_modules_stats_geolocalisation_configure'), null, $CLICSHOPPING_StatsGeolocalisation->link('Configure'), 'warning'); ?></span>

            <?php echo HTML::form('export_customer', $CLICSHOPPING_StatsGeolocalisation->link('ExportOrders'));  ?>
              <span>
               <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#myModalExport">
                <?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('button_export_customer_info'); ?>
              </button>
              <div class="modal" id="myModalExport">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                      <h4 class="modal-title"><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('button_export_customer_info'); ?></h4>
                      <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body">
                      <div class="text-start">
                        <div class="row col-md-12">
                          <span class="col-md-12 text-end">
                          <?php echo HTML::button($CLICSHOPPING_StatsGeolocalisation->getDef('button_export_customer_info'), null, null, 'primary'); ?>
                          </span>
                          <div class="separator"></div>
                          <?php
                          $QColumnsTable = $CLICSHOPPING_StatsGeolocalisation->db->query('SHOW COLUMNS
                                                                                         FROM :table_orders
                                                                                         where Field = \'orders_id\'
                                                                                          or  Field = \'customers_id\'
                                                                                          or Field = \'orders_id\'
                                                                                          or Field = \'customers_id\'
                                                                                          or Field = \'customers_street_address\'
                                                                                          or Field = \'customers_suburb\'
                                                                                          or Field = \'customers_city\'
                                                                                          or Field = \'customers_postcode\'
                                                                                          or Field = \'customers_state\'
                                                                                          or Field = \'customers_country\'
                                                                                          or Field = \'orders_status\'
                                                                                          or Field = \'customers_group_id\'
                                                                                          or Field = \'client_computer_ip\'
                                                                                          or Field = \'orders_archive\'
                                                                                          or Field = \'latitude\'
                                                                                          or Field = \'longitude\'
                                                                                          or Field = \'products_id\'
                                                                                          or Field = \'products_name\'
                                                                                          or Field = \'products_model\'
                                                                                          or Field = \'products_price\'
                                                                                          or Field = \'products_quantity\'
                                                                                         ');
                          $array_columns = $QColumnsTable->fetchAll();

                          foreach ( $array_columns as $result) {
                            if ($result['Field'] != 'date_added') {
                              echo '<span class="col-md-12">' . HTML::checkboxField($result['Field'])  . '  ' . $result['Field'] . ' </span>';
                            }
                          }
                          ?>
                        </div>
                      </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                      <span class="text-start"><button type="button" class="btn btn-warning" data-bs-dismiss="modal"><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_close'); ?></button></span>
                    </div>
                  </div>
                </div>
              </div>
            </span>
            </form>

            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#myModal">
              <?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_help'); ?>
            </button>
            <div class="modal" id="myModal">
              <div class="modal-dialog">
                <div class="modal-content">
                  <!-- Modal Header -->
                  <div class="modal-header">
                    <h4 class="modal-title"><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_help'); ?></h4>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                  </div>
                  <!-- Modal body -->
                  <div class="modal-body">
                    <div class="text-start"><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_help_description'); ?></div>
                  </div>
                  <!-- Modal footer -->
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <?php echo HTML::form('search', $CLICSHOPPING_StatsGeolocalisation->link('StatsGeolocalisationOrder'), 'post', '', ['session_id' => true]); ?>

  <div class="row col-md-12">
    <div class="col-md-10 card">
      <div class="row" id="tab1Content1">
        <div class="col-md-2" id="country_name">
          <div class="form-group row">
            <span><?php echo HTML::inputField('country_name', null, 'placeholder="Country name"'); ?>&nbsp;</span>
          </div>
        </div>
        <div class="col-md-2" id="region">
          <div class="form-group row">
            <span><?php echo HTML::inputField('region', null, 'placeholder="Region"'); ?>&nbsp;</span>
          </div>
        </div>
        <div class="col-md-2" id="city">
          <div class="form-group row">
            <span><?php echo HTML::inputField('city', null, 'placeholder="City"'); ?>&nbsp;</span>
          </div>
        </div>
        <div class="col-md-2" id="zip">
          <div class="form-group row">
            <span><?php echo HTML::inputField('postal_code', null, 'placeholder="Zip"'); ?>&nbsp;</span>
          </div>
        </div>
        <div class="col-md-2" id="customers_group_id">
          <div class="form-group row">
            <span><?php echo HTML::selectField('customers_group_id', GroupsB2BAdmin::getAllGroups(), $customers_group_id); ?>&nbsp;</span>
          </div>
        </div>
        <div class="col-md-2" id="language_id">
          <div class="form-group row">
            <span><?php echo HTML::selectField('language_id', $CLICSHOPPING_Language->getAllLanguage(true), $language); ?>&nbsp;</span>
          </div>
        </div>
      </div>
      <div class="separator"></div>

      <div class="row" id="tab1Content2">
        <div class="col-md-2" id="products_name">
          <div class="form-group row">
            <span><?php echo HTML::inputField('products_name', null, 'placeholder="Products Name"'); ?>&nbsp;</span>
          </div>
        </div>
        <div class="col-md-2" id="products_model">
          <div class="form-group row">
            <span><?php echo HTML::inputField('products_model', null, 'placeholder="Products Model"'); ?>&nbsp;</span>
          </div>
        </div>
        <div class="col-md-2" id="date_start">
          <div class="form-group row">
            <span><?php echo HTML::inputField('date_start', null, null, 'date'); ?>&nbsp;</span>
          </div>
        </div>
        <div class="col-md-2" id="date_end">
          <div class="form-group row">
            <span><?php echo HTML::inputField('date_end', null, null, 'date'); ?>&nbsp;</span>
          </div>
        </div>
      </div>

      <div class="row col-md-12" id="tab1Content2">
        <span class="col-md-2">
          <ul class="list-group-slider list-group-flush">
            <span class="text-slider"><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_view'); ?></span>
            <li class="list-group-item-slider">
              <label class="switch">
                <?php echo HTML::checkboxField('view', true, null, 'class="success"'); ?>
                <span class="slider"></span>
              </label>
            </li>
          </ul>
        </span>

        <span class="col-md-2">
          <ul class="list-group-slider list-group-flush">
            <span class="text-slider"><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_with_order_instance'); ?></span>
            <li class="list-group-item-slider">
              <label class="switch">
                <?php echo HTML::checkboxField('order_instance', false, null, 'class="success"'); ?>
                <span class="slider"></span>
              </label>
            </li>
          </ul>
        </span>

        <span class="col-md-2">
          <ul class="list-group-slider list-group-flush">
            <span class="text-slider"><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_with_order_pending'); ?></span>
            <li class="list-group-item-slider">
              <label class="switch">
                <?php echo HTML::checkboxField('order_pending', false, null, 'class="success"'); ?>
                <span class="slider"></span>
              </label>
            </li>
          </ul>
        </span>

        <span class="col-md-2">
          <ul class="list-group-slider list-group-flush">
            <span class="text-slider"><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_with_order_delivery'); ?></span>
            <li class="list-group-item-slider">
              <label class="switch">
                <?php echo HTML::checkboxField('order_delivery', false, null, 'class="success"'); ?>
                <span class="slider"></span>
              </label>
            </li>
          </ul>
        </span>

        <span class="col-md-2">
          <ul class="list-group-slider list-group-flush">
            <span class="text-slider"><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_with_order_cancelled'); ?></span>
            <li class="list-group-item-slider">
              <label class="switch">
                <?php echo HTML::checkboxField('order_cancelled', false, null, 'class="success"'); ?>
                <span class="slider"></span>
              </label>
            </li>
          </ul>
        </span>

        <span class="col-md-2">
          <ul class="list-group-slider list-group-flush">
            <span class="text-slider"><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_with_order_other'); ?></span>
            <li class="list-group-item-slider">
              <label class="switch">
                <?php echo HTML::checkboxField('order_other', false, null, 'class="success"'); ?>
                <span class="slider"></span>
              </label>
            </li>
          </ul>
        </span>
      </div>
      <div class="row col-md-12" id="tab1Content3">
        <div class="col-md-2" id="text_with_order_other">
          <div class="form-group row">
            <span class="col-md-2">
              <ul class="list-group-slider list-group-flush">
                <span class="text-slider"><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_with_archive'); ?></span>
                <li class="list-group-item-slider">
                  <label class="switch">
                    <?php echo HTML::checkboxField('order_archive', false, null, 'class="success"'); ?>
                    <span class="slider"></span>
                  </label>
                </li>
              </ul>
            </span>
          </div>
        </div>

        <div class="col-md-2  text-end id="text_with_order_other">
          <div class="form-group row">
            <span class="text-end"><?php echo HTML::button('Search', null, null, 'success', null, 'sm'); ?>&nbsp;</span>
          </div>
         </div>
      </div>
    </div>


    <div  class="col-md-2 card">
      <div class="col-md-12 form-group">
        <span><?php echo HTML::inputField('min_zoom', 2.5, 'placeholder="Min Zoom"'); ?>&nbsp;</span>
        <div class="separator"></div>
        <span><?php echo HTML::inputField('limit', $limit ,'placeholder="Data limit"'); ?>&nbsp;</span>
        <?php
        $sort_order_array = [
          ['id' => 'select', 'text' => $CLICSHOPPING_StatsGeolocalisation->getDef('text_sort_order')],
          ['id' => 'client_computer_ip', 'text' => $CLICSHOPPING_StatsGeolocalisation->getDef('text_ip')],
          ['id' => 'date_purchased', 'text' => $CLICSHOPPING_StatsGeolocalisation->getDef('text_date_purchased')],
          ['id' => 'products_name', 'text' => $CLICSHOPPING_StatsGeolocalisation->getDef('text_products_name')],
          ['id' => 'country_name', 'text' => $CLICSHOPPING_StatsGeolocalisation->getDef('text_country_name')],
          ['id' => 'region', 'text' => $CLICSHOPPING_StatsGeolocalisation->getDef('text_region')],
          ['id' => 'city', 'text' => $CLICSHOPPING_StatsGeolocalisation->getDef('text_city')],
          ['id' => 'postal_code', 'text' => $CLICSHOPPING_StatsGeolocalisation->getDef('text_postal_code')],
          ['id' => 'city', 'text' => $CLICSHOPPING_StatsGeolocalisation->getDef('text_city')],
          ['id' => 'products_price', 'text' => $CLICSHOPPING_StatsGeolocalisation->getDef('text_products_price')],
        ];
        ?>
        <span><?php echo HTML::selectField('sort_order', $sort_order_array); ?>
        <span>
          <ul class="list-group-slider list-group-flush">
            <span class="text-slider"><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_sheet'); ?></span>
            <li class="list-group-item-slider">
              <label class="switch">
                <?php echo HTML::checkboxField('display_sheet', false, null, 'class="success"'); ?>
                <span class="slider"></span>
              </label>
            </li>
          </ul>
        </span>
      </div>
    </div>
  </div>
</form>

  <div class="separator"></div>
  <div id="map" style="height: 700px; border: 1px solid #AAA;"></div>
<?php
  $arrays[] = [
    'name' => '',
    'url' => '',
    'lat' =>  '',
    'lng' =>  '',
  ];

  if ($view == 1) {
    $result_array = $CLICSHOPPING_GeolocalisationAdmin->geOrder();
    if (\is_array($result_array)) {
      foreach ($result_array as $value) {
        $total_order = $value['products_price'] * $value['products_quantity'];

        $localisation = $value['country_name'] . ' - '.  $value['region'] . '<br />' .
          $value['city']  . ' - ' .  $value['postal_code']  . ' <br /> ' .
          $value['products_name'] . '<br />' .
          $CLICSHOPPING_StatsGeolocalisation->getDef('text_products_price') . ' ' . $total_order;

        if (!empty($value['latitude'])) {
          $arrays[] = [
            'name' => $localisation,
            'url' => '',
            'lat' =>  $value['latitude'],
            'lng' =>  $value['longitude'],
          ];
        }
      }
    }
  }

  if (isset($_POST['display_sheet'])) {
    $result_array = $CLICSHOPPING_GeolocalisationAdmin->geOrder(true);
?>
  <div class="separator"></div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <td>
      <table class="table table-sm table-hover table-striped">
        <thead>
          <tr class="dataTableHeadingRow">
            <td><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_ip'); ?></td>
            <td><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_country'); ?></td>
            <td><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_region'); ?></td>
            <td><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_country_name'); ?></td>
            <td><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_city'); ?></td>
            <td><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_postal_code'); ?></td>
            <td><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_orders_status'); ?></td>
            <td><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_customers_group'); ?></td>
            <td><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_products_model'); ?></td>
            <td><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_products_name'); ?></td>
            <td><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_products_price'); ?></td>
            <td><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_products_quantity'); ?></td>
            <td><?php echo $CLICSHOPPING_StatsGeolocalisation->getDef('text_total_amount'); ?></td>
          </tr>
        </thead>
        <tbody>

        <?php
        foreach ($result_array as $value) {
        ?>
          <tr>
            <td><?php echo '<a href="https://ipinfo.io/' . $value['ip_address'] . '" target="_blank" rel="noreferrer">' . $value['ip_address'] . '</a>'; ?></td>
            <td><?php echo $value['country']; ?></td>
            <td><?php echo $value['region']; ?></td>
            <td><?php echo $value['country_name']; ?></td>
            <td><?php echo $value['city']; ?></td>
            <td><?php echo $value['postal_code']; ?></td>
            <td><?php echo $value['orders_status']; ?></td>
            <td><?php echo $value['customers_group_id']; ?></td>
            <td><?php echo $value['products_model']; ?></td>
            <td><?php echo $value['products_name']; ?></td>
            <td><?php echo $value['products_price']; ?></td>
            <td><?php echo $value['products_quantity']; ?></td>
            <td><?php echo $value['products_price'] * $value['products_quantity']; ?></td>
          </tr>
          <?php
        }
        ?>

        </tbody>
      </table>
    </td>
  </table>

  <?php
      }
  ?>
</div>
<div class="separator"></div>
<?php
  $obj = json_encode($arrays);
?>
<script>
  markers = <?php echo $obj; ?>

  var map = L.map( 'map', {
    center: [20.0, 5.0],
    minZoom: <?php echo $min_zoom; ?>,
    zoom: 3
  });

  L.tileLayer( 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    subdomains: ['a','b','c']
  }).addTo( map );

  for ( var i=0; i < markers.length; ++i )
  {
    L.marker( [markers[i].lat, markers[i].lng])
      .bindPopup( '<a href="' + markers[i].url + '" target="_blank">' + markers[i].name + '</a>' )
      .addTo( map );
  }
</script>


