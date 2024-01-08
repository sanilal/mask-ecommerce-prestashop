<?php
/**
 *  Order Fees Shipping
 *
 *  @author    motionSeed <ecommerce@motionseed.com>
 *  @copyright 2017 motionSeed. All rights reserved.
 *  @license   https://www.motionseed.com/en/license-module.html
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!class_exists('MotionSeedModule')) {
    include_once(dirname(__FILE__) . '/helpers/motionseed-module/MotionSeedModule.php');
}

class OrderFees_Shipping extends MotionSeedModule
{
    public static $WEIGHT_UNITS = array(
        'kg' => 1.0,
        'T' => 1000.0,
        'lb' => 0.45359237,
        'kip' => 453.59237
    );
    
    public static $VOLUME_UNITS = array(
        'm3' => 1.0,
        'cm3' => 0.000001,
        'in3' => 0.000016,
        'ft3' => 0.028317,
        'yd3' => 0.764555
    );
    
    public $weight_unit_default = null;
    public $volume_unit_default = null;
    
    public function __construct()
    {
        $this->name = 'orderfees_shipping';
        $this->tab = 'shipping_logistics';
        $this->version = '1.8.36';
        $this->author = 'motionSeed';
        $this->need_instance = 0;
        $this->ps_versions_compliancy['min'] = '1.6.0.0';

        parent::__construct();
        
        $this->displayName = $this->l('Advanced Shipping cost');
        $this->description = $this->l('Manage easily your shipping cost');

        $this->error = false;
        $this->secure_key = Tools::encrypt($this->name);
        $this->module_key = '3f8ccdf1109e18c34cc5f2d038c418dc';
        $this->id_product = 29332;
        $this->author_address = '0xF73fDBe05d46306A34b5eeB564e650b2f62B1041';
        
        $this->configurations = array(
            array(
                'name' => 'MS_ORDERFEES_SHIPPING_CONDITIONS_DISPLAY_SKU',
                'label' => 'Display SKU on Products Selection',
                'default' => '1'
            ),
            array(
                'name' => 'MS_ORDERFEES_SHIPPING_TYPES',
                'label' => '',
                'default' => 'country,zone,state,carrier,group,of_shipping_rule,shop'
            )
        );
        
        $this->weight_unit_default = Tools::strtolower(Configuration::get('PS_WEIGHT_UNIT'));
        $this->volume_unit_default = Tools::strtolower(Configuration::get('PS_DIMENSION_UNIT')) . '3';
        
        $this->type_context = Shop::getContext();
        $this->old_context = Context::getContext();
    }
    
    public function specificOverride($classname, $install = true)
    {
        if (Module::isEnabled('orderfees')) {
            $this->disabled_overrides = array('Cart');
        }
        
        return parent::specificOverride($classname, $install);
    }
    
    public function getContent()
    {
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminOrderFeesShipping'));
    }

    public function registerHooks()
    {
        return parent::registerHooks()
            && $this->registerHook('actionCartGetPackageShippingCost')
            /*&& $this->registerHook('actionCartSummary')*/;
    }
    
    public function hookActionCartGetPackageShippingCost(&$params)
    {
        $object = $params['object'];
        
        $items = Db::getInstance()->executeS(
            'SELECT sr.id_of_shipping_rule, GROUP_CONCAT(c.id_carrier SEPARATOR ",") AS carriers
            FROM '._DB_PREFIX_.'of_shipping_rule sr
            LEFT JOIN '._DB_PREFIX_.'of_shipping_rule_carrier src
                ON sr.id_of_shipping_rule = src.id_of_shipping_rule 
            LEFT JOIN '._DB_PREFIX_.'carrier c 
                ON c.id_reference = src.id_carrier
                    AND c.deleted = 0
            WHERE sr.active = 1
            GROUP BY sr.id_of_shipping_rule
            ORDER BY sr.priority ASC'
        );
        
        if (empty($items)) {
            return;
        }
        
        $product_list = $object->getProducts();
        $total = &$params['total'];
        $use_tax = &$params['use_tax'];
        $id_carrier = $params['id_carrier'];
        
        // Address
        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
            $cart = &$params['object'];
            $address_id = (int)$cart->id_address_invoice;
        } elseif (count($product_list)) {
            $prod = current($product_list);
            $address_id = (int)$prod['id_address_delivery'];
        } else {
            $address_id = null;
        }
        if (!Address::addressExists($address_id)) {
            $address_id = null;
        }
        
        // Products
        $free_shipping_products = array();
        
        foreach ($product_list as $product) {            
            $free_shipping_products[] = (int)$product['id_product'].'-'.(int)$product['id_product_attribute'];
        }
        
        // Carrier
        if (empty($id_carrier)) {
            $id_carrier = Configuration::get('PS_CARRIER_DEFAULT');
        }

        $carrier = new Carrier((int)$id_carrier, Configuration::get('PS_LANG_DEFAULT'));
        
        $items_disabled = array();

        foreach ($items as &$item) {
            if (in_array($item['id_of_shipping_rule'], $items_disabled)) {
                continue;
            }
            
            $shipping_rule = ShippingRule::factory($item['id_of_shipping_rule'], $object);
                
            if (!$shipping_rule->check()) {
                continue;
            }
            
            $carriers = $item['carriers'] != null ? explode(',', $item['carriers']) : array();
                
            if (empty($carriers) || !in_array($id_carrier, $carriers)) {
                if ($shipping_rule->carrier_restriction && ($shipping_rule->type & ShippingRule::IS_CARRIER)) {
                    $total = false;
                    $params['return'] = true;
                    
                    return;
                }
                
                if (!empty($carriers)) {
                    continue;
                }
            }
            
            if ($shipping_rule->of_shipping_rule_restriction) {
                $other_items = $items;

                foreach ($other_items as $other_item) {
                    if ($shipping_rule->id == $other_item['id_of_shipping_rule']) {
                        continue;
                    }

                    $combinable = Db::getInstance()->getValue(
                        'SELECT id_of_shipping_rule_1
                        FROM '._DB_PREFIX_.'of_shipping_rule_combination
                        WHERE (
                            id_of_shipping_rule_1 = ' . (int) $shipping_rule->id . '
                            AND id_of_shipping_rule_2 = ' . (int) $other_item['id_of_shipping_rule'] . '
                        ) OR (
                            id_of_shipping_rule_2 = ' . (int) $shipping_rule->id . '
                            AND id_of_shipping_rule_1 = ' . (int) $other_item['id_of_shipping_rule'] . '
                        )'
                    );

                    if (!$combinable) {
                        $items_disabled[] = $other_item['id_of_shipping_rule'];
                    }
                }
            }

            if ($shipping_rule->type == ShippingRule::IS_NONE && !$shipping_rule->package_restriction) {
                continue;
            }

            if ($shipping_rule->type & ShippingRule::IS_FREE_SHIPPING) {
                if ($shipping_rule->product_restriction && ($shipping_rule->type & ShippingRule::FREE_SHIPPING_ALL)) {
                    $free_shipping_products = array_diff(
                        $free_shipping_products,
                        $shipping_rule->restrictionsProducts(true)
                    );
                } else {
                    $free_shipping_products = array();

                    break;
                }
            }

            if ($shipping_rule->package_restriction) {
                $package_rule_groups = $shipping_rule->getPackageRuleGroups($shipping_rule);

                foreach ($package_rule_groups as $package_rule_group) {
                    $weight = 0;
                    $volume = 0;
                    
                    foreach ($shipping_rule->getProducts() as $product) {
                        if (isset($product['weight'])) {
                            $weight += $product['weight'] * $product['cart_quantity'];
                        }

                        if (isset($product['height']) && isset($product['width']) && isset($product['depth'])) {
                            $volume += (($product['height'] * $product['width'] * $product['depth']) * $product['cart_quantity']);
                        }
                    }
                    
                    $volumetric_weight = 0;

                    switch ($package_rule_group['unit']) {
                        case 'kg/m3':
                            $volumetric_weight = $this->volumeTo($volume, 'm3') * $package_rule_group['ratio'];
                            break;
                        case 'cm3/kg':
                            $volumetric_weight = $this->volumeTo($volume, 'cm3') / $package_rule_group['ratio'];
                            break;
                    }

                    $weight = max($volumetric_weight, $this->weightTo($weight, 'kg'));
                    $id_package_rule_group = $package_rule_group['id_package_rule_group'];

                    $package_rule = Db::getInstance()->getRow(
                        'SELECT pr.*, pr.currency,
                            IF(
                                pr.divider = 1,
                                pr.value,
                                ((CEIL(' . pSQL($weight) . ' / pr.round) * pr.round) * pr.value) / pr.divider 
                            ) AS price
                            FROM '._DB_PREFIX_.'of_shipping_rule_package_rule pr
                            WHERE pr.id_package_rule_group = ' . (int)$id_package_rule_group . '
                                AND CEIL(' . pSQL($weight) . ' / pr.round) * pr.round
                                    BETWEEN pr.range_start AND pr.range_end'
                    );
                    
                    if (empty($package_rule)) {
                        continue;
                    }

                    $carrier_tax = 0;

                    // Select carrier tax
                    if ($use_tax && !Tax::excludeTaxeOption()) {
                        $address = Address::initialize((int)$address_id);

                        if (Configuration::get('PS_ATCP_SHIPWRAP')) {
                            $carrier_tax = 0;
                        } else {
                            $carrier_tax = $carrier->getTaxesRate($address);
                        }
                    }

                    $package_rule['price'] *= 1 + ($carrier_tax / 100);

                    $total += (float)Tools::ps_round(
                        (float)$package_rule['price'],
                        (Currency::getCurrencyInstance(
                            (int)$package_rule['currency']
                        )->decimals * _PS_PRICE_DISPLAY_PRECISION_)
                    );
                }
            } else {
                $total += Tools::ps_round($shipping_rule->value($use_tax), _PS_PRICE_COMPUTE_PRECISION_);
            }
        }
        
        if (empty($free_shipping_products)) {
            $total = 0;
            $params['return'] = true;

            return;
        }
    }
    
    public function hookActionCartSummary($params)
    {
        $shipping_products = array();
        
        foreach ($params['products'] as $product) {
            $key = $product['id_product'] . '-' . $product['id_product_attribute'];
            
            $price = ShippingRule::getShippingCostByProduct($product['id_product'], $product['id_product_attribute'], true);
            
            $shipping_products[$key] = Tools::displayPrice($price);
        }
        
        return array('shipping_products' => $shipping_products);
    }
    
    public function weightTo($value, $to_unit, $from_unit = null)
    {
        if (!$from_unit) {
            $from_unit = $this->weight_unit_default;
        }
        
        return ($value * self::$WEIGHT_UNITS[$from_unit]) / self::$WEIGHT_UNITS[$to_unit];
    }
    
    public function volumeTo($value, $to_unit, $from_unit = null)
    {
        if (!$from_unit) {
            $from_unit = $this->volume_unit_default;
        }
        
        return  ($value * self::$VOLUME_UNITS[$from_unit]) / self::$VOLUME_UNITS[$to_unit];
    }
    
    public function getActualCurrency()
    {
        if ($this->type_context == Shop::CONTEXT_SHOP) {
            Shop::setContext($this->type_context, $this->old_context->shop->id);
        } elseif ($this->type_context == Shop::CONTEXT_GROUP) {
            Shop::setContext($this->type_context, $this->old_context->shop->id_shop_group);
        }

        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

        Shop::setContext(Shop::CONTEXT_ALL);

        return $currency;
    }
}
