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

  class ExportOrders extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $file = null;
    protected $use_site_template = false;

    public function execute()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if (isset($_POST['orders_id'])) $orders_id = 'o.orders_id,';
      if (isset($_POST['customers_id'])) $customers_id = 'o.customers_id,';
      if (isset($_POST['customers_street_address'])) $customers_street_address = 'o.customers_street_address,';
      if (isset($_POST['customers_suburb'])) $customers_suburb = 'o.customers_suburb,';
      if (isset($_POST['customers_city'])) $customers_city= 'o.customers_city,';
      if (isset($_POST['customers_postcode'])) $customers_postcode = 'o.customers_postcode,';
      if (isset($_POST['customers_state'])) $customers_state = 'o.customers_state,';
      if (isset($_POST['customers_country'])) $customers_country = 'o.customers_country,';
      if (isset($_POST['orders_status'])) $orders_status = 'o.orders_status,';
      if (isset($_POST['customers_group_id'])) $customers_group_id = 'o.customers_group_id,';
      if (isset($_POST['client_computer_ip'])) $client_computer_ip = 'o.client_computer_ip,';
      if (isset($_POST['orders_archive'])) $orders_archive = 'o.orders_archive,';
      if (isset($_POST['latitude'])) $latitude = 'o.latitude,';
      if (isset($_POST['longitude'])) $longitude = 'o.longitude,';

      if (isset($_POST['products_id'])) $products_id = 'op.products_id,';
      if (isset($_POST['products_name'])) $products_name = 'op.products_name,';
      if (isset($_POST['products_model'])) $products_model = 'op.products_model,';
      if (isset($_POST['products_price'])) $products_price = 'op.products_price,';
      if (isset($_POST['products_quantity'])) $products_quantity = 'op.products_quantity,';


      $Qexport = $CLICSHOPPING_Db->prepare('select distinct ' . $orders_id . '
                                                            ' . $customers_id . '
                                                            ' . $customers_street_address . '
                                                            ' . $customers_suburb . '
                                                            ' . $customers_city . '
                                                            ' . $customers_postcode . '
                                                            ' . $customers_state . '
                                                            ' . $customers_country . '
                                                            ' . $orders_status . '
                                                            ' . $customers_group_id . '
                                                            ' . $client_computer_ip . '
                                                            ' . $orders_archive . '
                                                            ' . $latitude . '
                                                            ' . $longitude . '
                                                            ' . $products_id . '
                                                            ' . $products_name . '
                                                            ' . $products_model . '
                                                            ' . $products_price . '
                                                            ' . $products_quantity . '
                                                            o.date_purchased
                                          from :table_orders o,
                                               :table_orders_products op
                                          where o.orders_id = op.orders_id                                     
                                      ');
      $Qexport->execute();

      $result = $Qexport->fetchAll();

      $delimiter = ';';
      $filename = 'export_orders_' . date('Y-m-d') . '.csv';

      //create a file pointer
      $f = fopen('php://memory', 'w');

      if (!empty($orders_id)) $array_field[] =  str_replace(',', '', $orders_id);
      if (!empty($customers_id)) $array_field[] =  str_replace(',', '', $customers_id);
      if (!empty($customers_street_address)) $array_field[] = str_replace(',', '', $customers_street_address);
      if (!empty($customers_suburb)) $array_field[] = str_replace(',', '', $customers_suburb);
      if (!empty($customers_city)) $array_field[] = str_replace(',', '', $customers_city);
      if (!empty($customers_postcode)) $array_field[] = str_replace(',', '', $customers_postcode);
      if (!empty($customers_state)) $array_field[] = str_replace(',', '', $customers_state);
      if (!empty($customers_country)) $array_field[] = str_replace(',', '', $customers_country);
      if (!empty($latitude)) $array_field[] = str_replace(',', '', $latitude);
      if (!empty($orders_status)) $array_field[] = str_replace(',', '', $orders_status);
      if (!empty($customers_group_id)) $array_field[] = str_replace(',', '', $customers_group_id);
      if (!empty($orders_archive)) $array_field[] = str_replace(',', '', $orders_archive);
      if (!empty($latitude)) $array_field[] = str_replace(',', '', $latitude);
      if (!empty($longitude)) $array_field[] = str_replace(',', '', $longitude);
      if (!empty($products_id)) $array_field[] = str_replace(',', '', $products_id);
      if (!empty($products_name)) $array_field[] = str_replace(',', '', $products_name);
      if (!empty($products_model)) $array_field[] = str_replace(',', '', $products_model);
      if (!empty($products_price)) $array_field[] = str_replace(',', '', $products_price);
      if (!empty($products_quantity)) $array_field[] = str_replace(',', '', $products_quantity);

      $array_field[] = 'date_purchased';

      fputcsv($f, $array_field, $delimiter);

      foreach ($result as $line) {
        // generate csv lines from the inner arrays
        fputcsv($f, $line, ';');
      }

      //move back to beginning of file
      fseek($f, 0);

      //set headers to download file rather than displayed
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename=' . $filename . ';');

      //output all remaining data on a file pointer
      fpassthru($f);

      exit;
    }
  }