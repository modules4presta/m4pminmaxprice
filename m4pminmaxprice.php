<?php

/**
 * LICENCE
 *
 * ALL RIGHTS RESERVED.
 * YOU ARE NOT ALLOWED TO COPY/EDIT/SHARE/WHATEVER.
 *
 * IN CASE OF ANY PROBLEM CONTACT AUTHOR.
 *
 *  @author    Jan Kołodziej (contact@modules4presta.io)
 *  @copyright modules4presta.io
 *  @license   ALL RIGHTS RESERVED
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/classes/Database.php';

class M4pMinMaxPrice extends Module
{
    public function __construct()
    {
        $this->name = 'm4pminmaxprice';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Modules4Presta.io';
        $this->db = Db::getInstance();
        $this->context = Context::getContext();
        $this->need_instance = 0;
        $this->_path = _PS_MODULE_DIR_ . $this->name;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_,
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Min and max price for product');
        $this->description = $this->l('Module to set min and max price for product which will be set by admin.');
    }

    /**
     * Installs the module and registers required hooks
     * Creates necessary database tables
     */
    public function install(): bool
    {
        if (!parent::install()) {
            return false;
        } elseif (!$this->registerHook('actionProductUpdate')) {
            return false;
        } elseif (!Database::create()) {
            return false;
        }

        return true;
    }

    /**
     * Uninstalls the module and removes database tables
     */
    public function uninstall(): bool
    {
        if (!parent::uninstall()) {
            return false;
        } elseif (!Database::drop()) {
            return false;
        }

        return true;
    }

    /**
     * Insert price for product
     */
    private function insertPrices(
        int $idProduct,
        ?float $min,
        ?float $max,
        int $idProductAttribute = 0
    ): bool {
        $sql = "INSERT INTO `" . _DB_PREFIX_ . "m4pminmaxprice_prices` (`id_product`, `id_product_attribute`, `min`, `max`)
            VALUES (" . pSQL($idProduct) . ", " . pSQL($idProductAttribute) . ", " . pSQL($min) . ", " . pSQL($max) . ")
            ON DUPLICATE KEY UPDATE
                `min` = VALUES(`min`),
                `max` = VALUES(`max`);";

        return $this->db->execute($sql) ?? false;
    }

    /**
     * Get price for attribute
     * 
     * @param int $idProduct
     * @param ?int $idProductAttribute
     */
    private function getPrices(int $idProduct, int $idProductAttribute = 0): array
    {
        $sql = "SELECT `min`, `max` FROM " . _DB_PREFIX_ . "m4pminmaxprice_prices
            WHERE `id_product` = " . pSQL($idProduct) . "
                AND `id_product_attribute` = " . pSQL($idProductAttribute);

        $results = $this->db->getRow($sql);
        if (!empty($results)) {
            return $results;
        }

        return ['min' => 0, 'max' => 0];
    }

    /**
     * Render template to prices
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        $preparedPrices = [];

        $sql = "SELECT id_product_attribute FROM " . _DB_PREFIX_ . "product_attribute
            WHERE id_product = " . pSQL((int) $params['id_product']);

        $attributes = $this->db->executeS($sql);

        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $prices = $this->getPrices(
                    (int) $params['id_product'],
                    (int) $attribute['id_product_attribute']
                );

                $preparedPrices[] = [
                    'id_product_attribute' => $attribute['id_product_attribute'],
                    'name' => Product::getProductName((int) $params['id_product'], (int) $attribute['id_product_attribute']),
                    'min' => $prices['min'],
                    'max' => $prices['max'],
                ];
            }
        } else {
            $price = $this->getPrices(
                (int) $params['id_product']
            );

            $preparedPrices[] = [
                'id_product_attribute' => 0,
                'name' => Product::getProductName((int) $params['id_product']),
                'min' => $price['min'],
                'max' => $price['max'],
            ];
        }

        $this->context->smarty->assign([
            'prices' => $preparedPrices,
        ]);

        return $this->context->smarty->fetch('module:' . $this->name . '/views/templates/admin/configuration.tpl');
    }

    /**
     * Hook while update product
     * 
     * @param array $params
     */
    public function hookActionProductUpdate(array $params): bool
    {
        $idProduct = $params['id_product'];

        foreach ($params['m4pminmaxprice_prices'] as $idProductAttribute => $prices) {
            $insertedPrice = $this->insertPrices(
                (int) $idProduct,
                (int) $idProductAttribute,
                (float) $prices['min'],
                (float) $prices['max']
            );

            if (!$insertedPrice) {
                return false;
            }
        }

        return true;
    }
}
