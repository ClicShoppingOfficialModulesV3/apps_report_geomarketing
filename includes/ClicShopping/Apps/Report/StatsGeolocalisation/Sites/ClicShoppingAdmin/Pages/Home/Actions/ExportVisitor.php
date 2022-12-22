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

  use ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Config\DP\Params\cards;
  use ClicShopping\OM\Registry;

  class ExportVisitor extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $file = null;
    protected $use_site_template = false;

    public function execute()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if (isset($_POST['id'])) $id = 'id,';
      if (isset($_POST['ip_address'])) $ip_address = 'ip_address,';
      if (isset($_POST['country'])) $country = 'country,';
      if (isset($_POST['country_name'])) $country_name = 'country_name,';
      if (isset($_POST['region'])) $region = 'region,';
      if (isset($_POST['region_name'])) $region_name = 'region_name,';
      if (isset($_POST['city'])) $city = 'city,';
      if (isset($_POST['postal_code'])) $postal_code = 'postal_code,';
      if (isset($_POST['latitude'])) $latitude = 'latitude,';
      if (isset($_POST['longitude'])) $longitude = 'longitude,';
      if (isset($_POST['url'])) $url = 'url,';
      if (isset($_POST['products_name'])) $products_name = 'products_name,';
      if (isset($_POST['products_id'])) $products_id = 'products_id,';
      if (isset($_POST['products_ean'])) $products_ean = 'products_ean,';
      if (isset($_POST['categories_id'])) $categories_id = 'categories_id,';
      if (isset($_POST['categories_name'])) $categories_name = 'categories_name,';
      if (isset($_POST['brand_name'])) $brand_name = 'brand_name,';
      if (isset($_POST['customers_id'])) $customers_id = 'customers_id,';
      if (isset($_POST['language_id'])) $language_id = 'language_id,';
      if (isset($_POST['google_position'])) $google_position = 'google_position,';

      $Qexport = $CLICSHOPPING_Db->prepare('select ' . $id . '
                                                  ' . $ip_address . '
                                                  ' . $country . '
                                                  ' . $country_name . '
                                                  ' . $region . '
                                                  ' . $region_name . '
                                                  ' . $city . '
                                                  ' . $postal_code . '
                                                  ' . $latitude . '
                                                  ' . $longitude . '
                                                  ' . $url . '
                                                  ' . $products_name . '
                                                  ' . $products_id . '
                                                  ' . $products_ean . '
                                                  ' . $categories_id . '
                                                  ' . $categories_name . '
                                                  ' . $brand_name . '
                                                  ' . $customers_id . '
                                                  ' . $language_id . '
                                                  ' . $google_position . '
                                                  date_added
                                            from :table_info_customer_tracking
                                         ');
      $Qexport->execute();

      $result = $Qexport->fetchAll();

      $delimiter = ';';
      $filename = 'export_visitor_' . date('Y-m-d') . '.csv';

      //create a file pointer
      $f = fopen('php://memory', 'w');


      if (!empty($id)) $array_field[] =  str_replace(',', '', $id);
      if (!empty($ip_address)) $array_field[] = str_replace(',', '', $ip_address);
      if (!empty($country)) $array_field[] = str_replace(',', '', $country);
      if (!empty($country_name)) $array_field[] = str_replace(',', '', $country_name);
      if (!empty($region)) $array_field[] = str_replace(',', '', $region);
      if (!empty($region_name)) $array_field[] = str_replace(',', '', $region_name);
      if (!empty($city)) $array_field[] = str_replace(',', '', $city);
      if (!empty($postal_code)) $array_field[] = str_replace(',', '', $postal_code);
      if (!empty($latitude)) $array_field[] = str_replace(',', '', $latitude);
      if (!empty($longitude)) $array_field[] = str_replace(',', '', $longitude);
      if (!empty($url)) $array_field[] = str_replace(',', '', $url);
      if (!empty($products_name)) $array_field[] = str_replace(',', '', $products_name);
      if (!empty($products_id)) $array_field[] = str_replace(',', '', $products_id);
      if (!empty($products_ean)) $array_field[] = str_replace(',', '', $products_ean);
      if (!empty($categories_id)) $array_field[] = str_replace(',', '', $categories_id);
      if (!empty($categories_name)) $array_field[] = str_replace(',', '', $categories_name);
      if (!empty($brand_name)) $array_field[] = str_replace(',', '', $brand_name);
      if (!empty($customers_id)) $array_field[] = str_replace(',', '', $customers_id);
      if (!empty($language_id)) $array_field[] = str_replace(',', '', $language_id);
      if (!empty($google_position)) $array_field[] = str_replace(',', '', $google_position);

      $array_field[] = 'date_added';

      fputcsv($f, $array_field, $delimiter);

      foreach ($result as $line) {
        fputcsv($f, $line, ';');
      }

      //move back to beginning of file
      fseek($f, 0);

      //set headers to download file rather than displayed
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename= ' . $filename . ';');

      //output all remaining data on a file pointer
      fpassthru($f);

      exit;

    }
  }