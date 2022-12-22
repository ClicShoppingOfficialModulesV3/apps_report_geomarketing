<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Report\StatsGeolocalisation\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Report\StatsGeolocalisation\Classes\Shop\GeolocalisationShop;

  class GeolocalisationAdmin
  {
    /**
     * @var bool|\DB|null
     */
    private $db;
    /**
     * @var bool|null
     */
    private $GeolocalisationShop;
    /**
     * @var string
     */
    private string|array $country_name;
    /**
     * @var string
     */
    private string|array $region;
    /**
     * @var string
     */
    private string|array $products_name;
    /**
     * @var string
     */
    private string|array $city;
    /**
     * @var string
     */
    private string|array $postal_code;
    /**
     * @var int|string
     */
    private string|int|array $language_id;
    /**
     * @var string
     */
    private string|array $date_start;
    /**
     * @var string
     */
    private string|array $date_end;
    /**
     * @var int|string
     */
    private string|int $order_instance;
    /**
     * @var int|string
     */
    private string|int $order_pending;
    /**
     * @var int|string
     */
    private string|int $order_delivery;
    /**
     * @var int|string
     */
    private string|int $order_other;
    /**
     * @var int
     */
    private $orders_status;
    /**
     * @var string
     */
    private string $brand_name;
    /**
     * @var int
     */
    private int $archive;

    public function __construct()
    {
      $this->db = Registry::get('Db');
      Registry::set('GeolocalisationShop', new GeolocalisationShop());
      $this->GeolocalisationShop = Registry::get('GeolocalisationShop');

      if (isset($_POST['country_name'])) $this->country_name = HTML::sanitize($_POST['country_name']) ?? $this->country_name = '';
      if (isset($_POST['region'])) $this->region = HTML::sanitize($_POST['region']) ?? $this->region = '';
      if (isset($_POST['products_name'])) $this->products_name = HTML::sanitize($_POST['products_name']) ?? $this->products_name = '';
      if (isset($_POST['city'])) $this->city = HTML::sanitize($_POST['city']) ?? $this->city = '';
      if (isset($_POST['postal_code'])) $this->postal_code = HTML::sanitize($_POST['postal_code']) ?? $this->postal_code = '';
      if (isset($_POST['customers_group_id'])) $this->customers_group_id = HTML::sanitize($_POST['customers_group_id']) ?? $this->customers_group_id = 99;

      if (isset($_POST['customer_registred'])) {
        $this->customers_id = 1;
      } else {
        $this->customers_id = 0;
      }

      if (isset($_POST['products_description'])) {
        $this->products_description = 1;
      } else {
        $this->products_description = 0;
      }

      if (isset($_POST['categories_name'])) $this->categories_name = HTML::sanitize($_POST['categories_name']) ?? $this->categories_name = '';
      if (isset($_POST['brand_name'])) $this->brand_name = HTML::sanitize($_POST['brand_name']) ?? $this->brand_name = null;
      if (isset($_POST['products_model'])) $this->products_model = HTML::sanitize($_POST['products_model']) ?? $this->products_model = '';

      if (HTML::sanitize($_POST['date_start'])  != '') {
        $this->date_start = HTML::sanitize($_POST['date_start']);
      } else {
        $this->date_start = '2008-01-01';
      }

      if (HTML::sanitize($_POST['date_end']) != '') {
        $this->date_end = HTML::sanitize($_POST['date_end']);
      } else {
        $this->date_end = '2100-01-01';
      }

      if (!isset($_POST['language_id'])) {
        $this->language_id = 0;
      } else {
        $this->language_id = HTML::sanitize($_POST['language_id']);
      }

      if (isset($_POST['order_archive'])) {
        $this->archive = 1;
      } else {
        $this->archive = 0;
      }

      if (isset($_POST['categories_id'])) {
        $this->categories_id = HTML::sanitize($_POST['categories_id']);
      } else {
        $this->categories_id = 0;
      }

      if (isset($_POST['order_instance']))  {
        $this->order_instance = 1;
      } else {
        $this->order_instance = 0;
      }

      if (isset($_POST['order_pending'])) {
        $this->order_pending = 2;
      } else {
        $this->order_pending = 0;
      }

      if (isset($_POST['order_delivery'])) {
        $this->order_delivery = 3;
      } else {
        $this->order_delivery = 0;
      }

      if (isset($_POST['order_cancelled'])) {
        $this->order_cancelled = 4;
      } else {
        $this->order_cancelled = 4;
      }

      if (isset($_POST['order_other'])) {
        $this->order_other = 5;
      } else {
        $this->order_other = 0;
      }
    }

    /**
     * @return array
     */
    public function getDataNoOrder(bool $limit_search = false) :array
    {
      if($limit_search === true) {
        if (isset($_POST['limit']) && !empty($_POST['limit'])) {
          $limit = ' limit ' . (int)HTML::sanitize($_POST['limit']);
        } else {
          $limit = '';
        }
      } else {
        $limit = '';
      }

      if ($this->customers_id == 1 && !\is_null($this->customers_id)) {
        $customers_id = ' and customers_id > 0';
      } else {
        $customers_id = ' and customers_id = 0';
      }

      if ($this->language_id == '') {
        $language = ' and language_id > 0';
      } else {
        $language = ' and language_id = ' . (int)$this->language_id;
      }

      if ($this->products_description == 1) {
        $products_id = 'products_id > 0';
      } else {
        $products_id = 'products_id = 0';
      }

      if (isset($_POST['sort_order']) && $_POST['sort_order'] !== 'select') {
        $order_by = ' order by ' . HTML::sanitize($_POST['sort_order']);
      } else {
        $order_by = ' order by date_added desc';
      }

      $GeoSql = $this->db->prepare('select ip_address,
                                            country,    
                                           country_name,
                                           region,
                                           city,
                                           url,
                                           postal_code,
                                           latitude,
                                           longitude,
                                           products_id,
                                           products_name,
                                           categories_id,
                                           categories_name,
                                           brand_name,
                                           customers_id,
                                           language_id,
                                           date_added
                                      from :table_info_customer_tracking
                                      where ' . $products_id . '
                                      ' . $customers_id . '
                                      ' . $language . '
                                      and country_name like :country_name
                                      and region like :region
                                      and city like :city
                                      and postal_code like :postal_code
                                      and products_name like :products_name
                                      and categories_name like :categories_name
                                      and brand_name like :brand_name
                                      and date_added between :date_start and :date_end
                                      ' . $order_by . '
                                      ' . $limit . '
                                      
                                      ');

      $GeoSql->bindValue(':country_name', '%' . $this->country_name . '%');
      $GeoSql->bindValue(':region', '%' . $this->region . '%');
      $GeoSql->bindValue(':city', '%' . $this->city . '%');
      $GeoSql->bindValue(':postal_code', '%' . $this->postal_code . '%');
      $GeoSql->bindValue(':products_name', '%' . $this->products_name . '%');
      $GeoSql->bindValue(':categories_name', '%' . $this->categories_name . '%');
      $GeoSql->bindValue(':brand_name', '%' . $this->brand_name . '%');
      $GeoSql->bindValue(':date_start', $this->date_start);
      $GeoSql->bindValue(':date_end', $this->date_end);

      $GeoSql->execute();

      $result = $GeoSql->fetchAll();

      return $result;
    }

    /**
     *  Check if latitude or longitude exist or not
     */

    public function checkGeolocalisationOrder()
    {

      $GeoSql = $this->db->prepare('select orders_id,
                                           client_computer_ip,
                                           latitude,
                                           longitude                                          
                                    from :table_orders
                                    where (latitude is null or longitude is null)
                                    ');
      $GeoSql->execute();

      while($GeoSql->fetch()) {
        if ($this->GeolocalisationShop->checkIP($GeoSql->value('client_computer_ip')) === true) {
           $result = $this->GeolocalisationShop->getData($GeoSql->value('client_computer_ip'));

          $latitude = $result['latitude'];
          $longitude =  $result['longitude'];

          $update_array = ['orders_id' => $GeoSql->valueInt('orders_id')];

          $array = [
            'latitude' => $latitude,
            'longitude' => $longitude
          ];

          $this->db->save('orders', $array, $update_array);
        }
      }
    }

    /**
     * @return array
     */
    public function geOrder() :array
    {
      $this->checkGeolocalisationOrder();

      if (\is_null($this->order_instance) && \is_null($this->order_pending) && \is_null($this->order_delivery) && \is_null($this->order_cancelled) && \is_null($this->order_other)) {
        $orders_status = 'orders_status >= 1';
      } else {
        if (!\is_null($this->order_instance)) $orders_status = 'orders_status = 1';
        if (!\is_null($this->order_pending)) $orders_status = 'orders_status = 2';
        if (!\is_null($this->order_delivery)) $orders_status = 'orders_status = 3';
        if (!\is_null($this->order_cancelled)) $orders_status = 'orders_status = 4';
        if (!\is_null($this->order_other)) $orders_status = 'orders_status >=  5';
      }

      if ($this->customers_group_id == 99) {
        $customers_group_id = 'customers_group_id >=  0';
      } else {
        $customers_group_id = $this->customers_group_id;
      }

      if ($this->archive == 1) {
        $archive = 'and orders_archive >= 0 ';
      } else {
        $archive = 'and orders_archive = 0 ';
      }

      if (isset($_POST['sort_order']) && $_POST['sort_order'] !== 'select') {
        $order_by = 'order by ' . HTML::sanitize($_POST['sort_order']);
      } else {
        $order_by = 'order by date_purchased desc';
      }

      $GeoSql = $this->db->prepare('select distinct o.orders_id,
                                                    o.latitude,
                                                    o.longitude,                                          
                                                    o.customers_id,
                                                    o.customers_street_address,
                                                    o.customers_suburb,
                                                    o.customers_city as city,
                                                    o.customers_postcode as postal_code,
                                                    o.customers_state as region,
                                                    o.customers_country as country_name,
                                                    o.customers_telephone,
                                                    o.date_purchased,
                                                    o.orders_status,
                                                    o.customers_group_id,
                                                    o.client_computer_ip,
                                                    o.orders_archive,
                                                    op.products_id,
                                                    op.products_name,
                                                    op.products_model,
                                                    op.products_price,
                                                    op.products_quantity
                                      from :table_orders o,
                                           :table_orders_products op
                                      where '. $orders_status . '
                                      and o.orders_id = op.orders_id
                                      and o.customers_country like :country_name
                                      and o.customers_state like :region
                                      and o.customers_city like :city
                                      and o.customers_postcode like :postal_code
                                      and op.products_name like :products_name
                                      and op.products_model like :products_model                                    
                                      and customers_group_id = :customers_group_id
                                      and date_purchased between :date_start and :date_end
                                      ' . $archive . '
                                      ' . $order_by . '
                                      ');

      $GeoSql->bindValue(':country_name', '%' . $this->country_name . '%');
      $GeoSql->bindValue(':region', '%' . $this->region . '%');
      $GeoSql->bindValue(':city', '%' . $this->city . '%');
      $GeoSql->bindValue(':postal_code', '%' . $this->postal_code . '%');
      $GeoSql->bindValue(':products_name', '%' . $this->products_name . '%');
      $GeoSql->bindValue(':products_model', '%' . $this->products_model . '%');
      $GeoSql->bindInt(':customers_group_id', $customers_group_id);
      $GeoSql->bindValue(':date_start', $this->date_start);
      $GeoSql->bindValue(':date_end', $this->date_end);

      $GeoSql->execute();

      $result = $GeoSql->fetchAll();

      return $result;
    }


    /**
     * @return string
     */
    public function getGoogleMap(): string
    {
      $map = 'https://www.google.com/maps/place/' . $this->getPostalCode() . '+' . $this->getCity() . ',+' . $this->getCountryName() . '/@' . $this->geoLocalisationLatitude() . ',' . $this->geoLocalisationLongitude() . ',12z/data=!4m5!3m4!1s0x12cc413e1499c8a3:0xdc4d0f81bae4deef!8m2!3d43.950242!4d6.810042';

      return $map;
    }

  }