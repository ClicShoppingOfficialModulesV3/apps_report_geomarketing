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

  namespace ClicShopping\Apps\Report\StatsGeolocalisation\Classes\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Is;
  use ClicShopping\OM\CLICSHOPPING;

  class GeolocalisationShop
  {
    private $ip;
    private $db;
    private $lang;

    public function __construct()
    {
      $this->ip = HTTP::getIpAddress();
      $this->db = Registry::get('Db');
      $this->lang = Registry::get('Language');
      $this->checkSpider();
      $this->checkIP();
    }

    /**
     * @return bool
     */
    private function checkSpider() :bool
    {
       if ($this->blockSpider() === true) {
         return false;
       } else {
         return true;
       }
    }

    /**
     * @return bool
     */
    public function checkIP($ip = null): bool
    {
      if (!\is_null($ip)) {
        if ($ip == '::1') {
          $result = false;
        } else {
          $result = true;
        }
      } else {
        if (\is_null($ip)) {
          if (!Is::IpAddress($this->ip)) {
            $result = false;
          } else {
            $result = true;
          }

          $array_remove = explode (  ',' ,  CLICSHOPPING_APP_STATS_GEOLOCALISATION_SG_EXCLUDE_IP);

            if(\is_array($array_remove)) {
            foreach ($array_remove as $item) {
              $result = false;
            }
          }
        }
      }

      return $result;
    }

    /**
     * @return array
     */
    public function getData($ip = null): array
    {
      if (\is_null($ip)) {
        $array_response = [
          'url' => 'https://freegeoip.app/json/' . $this->ip
        ];
      } else {
        $array_response = [
          'url' => 'https://freegeoip.app/json/' . $ip
        ];
      }

      $data = @HTTP::getResponse($array_response);

      $result = json_decode($data, true, 512, JSON_THROW_ON_ERROR);


      $this->city = $result['city'];
      $this->region = $result['region_code'];
      $this->region_name = $result['region_name'];
      $this->country_code = $result['country_code'];
      $this->country_name = $result['country_name'];
      $this->latitude = $result['latitude'];
      $this->longitude = $result['longitude'];
      $this->zip_code = $result['zip_code'];

      return $result;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
      return $this->city;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
      return $this->region;
    }

    /**
     * @return mixed
     */
    public function getRegionName()
    {
      return $this->region_name;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
      return $this->country_code;
    }

    /**
     * @return mixed
     */
    public function getCountryName()
    {
      return $this->country_name;
    }

    /**
     * @return string
     */
    public function geoLocalisationLatitude()
    {
      return $this->latitude;
    }

    /**
     * @return mixed
     */
    public function geoLocalisationLongitude()
    {
      return $this->longitude;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
       return $this->zip_code;
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
    private function getProductsEAN(): ?string
    {
      $CLICSHOPPING_ProductsCommon= Registry::get('ProductsCommon');

      $products_id = $this->getProductId();

      if ($products_id !== 0) {
        $products_name = $CLICSHOPPING_ProductsCommon->getProductsEAN($products_id);
      } else {
        $products_name= '';
      }

      return $products_name;
    }

    /**
     * @return string
     */
    private function getBrandName(): ?string
    {
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

      $products_id = $this->getProductId();

      if ($products_id !== 0 && !\is_null($products_id)) {
        $brand_name = $CLICSHOPPING_ProductsCommon->getProductsManufacturer($products_id);

      } else {
        if (!\is_null(strpos( $_SERVER['REQUEST_URI'], 'manufacturersId'))) {;
          $params = $this->explodeUrl();
          $result = $params[\count($params)-1]; // 11

          $manufacturers_id = str_replace('manufacturers_id-', '', $result);

          $Qmanufacturer = $this->db->prepare('select manufacturers_name
                                                from :table_manufacturers
                                                where manufacturers_id = :manufacturers_id
                                              ');
          $Qmanufacturer->bindInt(':manufacturers_id', (int)$manufacturers_id);

          $Qmanufacturer->execute();

          $brand_name = $Qmanufacturer->value('manufacturers_name');
        } else {
          $brand_name = '';
        }
      }

      return $brand_name;
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
       if (!\is_null(strpos( $_SERVER['REQUEST_URI'], 'cPath'))) {;
          $params = $this->explodeUrl();
          $result = $params[\count($params)-1]; // 11

         $categories_id = str_replace('cPath-', '', $result);

       } else {
         $categories_id = 0;
       }
      }

      return (int)$categories_id;
    }

    /**
     * @return string
     */
    private function getCategoriesName() :string
    {
      $categories_id = $this->getCategoriesId();

      if ($categories_id != 0) {
        $Qcategories = $this->db->prepare('select categories_name
                                           from :table_categories_description
                                           where categories_id = :categories_id
                                           and language_id = :language_id
                                          ');
        $Qcategories->bindInt('categories_id', $categories_id);
        $Qcategories->bindInt('language_id', $this->lang->getid());
        $Qcategories->execute();

        $categories_name = $Qcategories->value('categories_name');
      } else {
        $categories_name = '';
      }

      return $categories_name;
    }


    /**
     * @return int
     */
    private function getCustomersId(): ?int
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $customers_id = $CLICSHOPPING_Customer->getID();

      return $customers_id;
    }


    /**
     *
     */
    public function saveData()
    {
      if ($this->checkSpider() !== false) {
        $this->getData();

        //currency
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
          'products_name' => $this->getProductName(),
          'products_ean' => $this->getProductsEAN(),
          'products_id' => (int)$this->getProductId(),
          'categories_id' => (int)$this->getCategoriesId(),
          'categories_name' => $this->getCategoriesName(),
          'brand_name' => $this->getBrandName(),
          'customers_id' => (int)$this->getCustomersId(),
          'language_id' => $this->lang->getid(),
          'google_position' => (int)$this->getGooglePosition(),
          'date_added' => 'now()'
        ];

        $this->db->save('info_customer_tracking', $sql_array);
      }
    }

    /**
     * @return int
     * To do
     */
    public function getGooglePosition(): ?int
    {
      return 0;
    }

    /**
     *
     */
    private function blockSpider() {
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
                return true;
              }
            }
          }
        }
      }
    }
  }