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

  namespace ClicShopping\Custom\Sites\Common;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Is;

  class InfoIPCustomers
  {
    protected $ip;
    protected $details;
    protected $curl;
    protected $map;
    protected $db;
    protected $nocache;

    public function __construct()
    {
      $this->ip = HTTP::getIpAddress();
      $this->checkIP();
      $this->db= Registry::get('Db');
      $this->blockSpider();
    }

    /**
     * @return bool
     */
    protected function checkIP(): bool
    {
      if (!Is::IpAddress($this->ip)) {
        return false;
      } else {
        return true;
      }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
      $array_response = [
  //        'url' => 'https://ipinfo.io/' . $this->ip . '/geo'
        'url' => 'https://freegeoip.app/json/' . $this->ip
      ];

      $data = @HTTP::getResponse($array_response);

      $result = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

      return $result;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
      $result = $this->getData();

      return $result['city'];
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
      $result = $this->getData();

      return $result['region_code'];
    }

    /**
     * @return string
     */
    public function getRegionName(): string
    {
      $result = $this->getData();

      return $result['region_name'];
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
      $result = $this->getData();

      return $result['country_code'];
    }

    /**
     * @return string
     */
    public function getCountryName(): string
    {
      $result = $this->getData();

      return $result['country_name'];
    }

    /**
     * @return string
     */
    public function geoLocalisationLatitude(): string
    {
      $result = $this->getData();

      return $result['latitude'];
    }

    /**
     * @return string
     */
    public function geoLocalisationLongitude(): string
    {
      $result = $this->getData();

      return $result['longitude'];
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
      $result = $this->getData();

      return $result['zip_code'];
    }

    /**
     * @return array|null
     */
    private function explodeUrl(): ?array
    {
      return explode('/', $_SERVER['REQUEST_URI']);
    }

    /**
     * @return string
     */
    public function getProductId(): ?int
    {
      $params = $this->explodeUrl();

      $result = $params[\count($params)-1]; // 11

      $products_id = str_replace('products_id-', '', $result);

      return (int)$products_id;
    }


    /**
     * @return string
     */
    private function getProductName(): ?string
    {
      $CLICSHOPPING_ProductsCommon= Registry::get('ProductsCommon');

      $products_id = $this->getProductId();

      if ($products_id !== 0) {
        $products_name = $CLICSHOPPING_ProductsCommon->getProductsName($products_id);
      } else {
        $products_name= '';
      }

      return $products_name;
    }

    /**
     * @return string
     */
    public function getProductsEan(): ?string
    {
      $CLICSHOPPING_ProductsCommon= Registry::get('ProductsCommon');

      $products_id = $this->getProductId();

      if ($products_id !== 0) {
        $products_name = $CLICSHOPPING_ProductsCommon->getProductsEan($products_id);
      } else {
        $products_name= '';
      }

      return $products_name;
    }

    /**
     * get the categories id in function the products id
     * @return int|null
     */
    private function getCategoriesId(): ?int
    {
      $products_id = $this->getProductId();

      if ($products_id !== 0) {
        $Qcategories = $this->db->prepare('select distinct categories_id
                                            from :table_products_to_categories
                                            where products_id = :products_id
                                            ');
        $Qcategories->bindInt('products_id', $products_id);
        $Qcategories->execute();

        $categories_id = $Qcategories->valueInt('categories_id');
      } else {
        $categories_id = 0;
      }

      return $categories_id;
    }


    /**
     * @return string
     */
    private function getCustomersId(): ?int
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $customers_id = $CLICSHOPPING_Customer->getID();

      return $customers_id;
    }


    /**
     * Create the Db
     */
    public function createDb()
    {
      $Qcheck = $this->db->query('show tables like ":table_info_customer_tracking"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
  CREATE TABLE :table_info_customer_tracking (
    id int NOT NULL auto_increment,
    ip_address varchar(255) NULL,
    country varchar(255) NULL,
    country_name varchar(255) NULL,
    region varchar(255) NULL,
    region_name varchar(255) NULL,
    city varchar(255) NULL,
    postal_code varchar(255) NULL,
    latitude varchar(255) NULL,
    longitude varchar(255) NULL,
    url varchar(255) NULL,
    product_name varchar(255) NULL,
    products_id int(11) DEFAULT (0),
    products_ean varchar (15) NULL,
    categories_id int(11) DEFAULT (0),
    customers_id int(11) DEFAULT (0),  
    language_id int(11) NOT NULL, 
    google_position int(0),
    date_added datetime,
    PRIMARY KEY (id)
  ) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
        $this->db->exec($sql);
      }
    }

    /**
     *
     */
    public function saveData()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      $this->createDb();
  //language
  //currency
  //manufacturer
      $sql_array = [
        'ip_address' => $this->ip,
        'country' => $this->getCountry(),
        'country_name' => $this->getCountryName(),
        'region' => $this->getRegion(),
        'region_name' => $this->getRegionName(),
        'city' => $this->getCity(),
        'postal_code' => $this->getPostalCode(),
        'latitude' => $this->geoLocalisationLatitude(),
        'longitude' => $this->geoLocalisationLongitude(),
        'url' => $_SERVER['REQUEST_URI'],
        'product_name' => $this->getProductName(),
        'products_id' => (int)$this->getProductId(),
        'products_ean' => $this->getProductsEan(),
        'categories_id' => (int)$this->getCategoriesId(),
        'customers_id' => (int)$this->getCustomersId(),
        'language_id' => $CLICSHOPPING_Language->getid(),
        'google_position' => (int)$this->getGooglePosition(),
        'date_added' => 'now()'
      ];

      $this->db->save('info_customer_tracking', $sql_array);
    }

    /**
     * @return string
     */
    public function getGoogleMap(): string
    {
      $map = 'https://www.google.com/maps/place/' . $this->getPostalCode() . '+' . $this->getCity() . ',+' . $this->getCountryName() . '/@' . $this->geoLocalisationLatitude() . ',' . $this->geoLocalisationLongitude() . ',12z/data=!4m5!3m4!1s0x12cc413e1499c8a3:0xdc4d0f81bae4deef!8m2!3d43.950242!4d6.810042';

      return $map;
    }

    /**
     * @return int
     */
    public function getGooglePosition(): ?int
    {
      return 0;
    }


    public function blockSpider() {
      $user_agent = '';

      if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
      }

      if (!empty($user_agent)) {
        $file_array = file(CLICSHOPPING::BASE_DIR . 'Sites/' . CLICSHOPPING::getSite() . '/Assets/spiders.txt');

        if (\is_array($file_array)) {
          foreach ($file_array as $spider) {
            if ((substr($spider, \strlen($spider) - 1, 1) == ' ') || (substr($spider, \strlen($spider) - 1, 1) == "\n")) {
              $spider = substr($spider, 0, \strlen($spider) - 1);
            }

            if (!empty($spider)) {
              if (str_contains($user_agent, $spider)) {
                break;
              }
            }
          }
        }
      }
    }
  }