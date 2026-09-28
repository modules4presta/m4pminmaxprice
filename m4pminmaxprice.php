<?php

/**
 * m4pminmaxprice
 *
 * @author    Modules4Presta <contact@modules4presta.io>
 * @copyright 2026 Nice Code sp. z o.o. (Modules4Presta)
 * @license   https://opensource.org/licenses/MIT MIT License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/classes/M4pMinMaxPriceDatabase.php';

class M4pMinMaxPrice extends Module
{
    public function __construct()
    {
        $this->name = 'm4pminmaxprice';
        $this->tab = 'pricing_promotion';
        $this->version = '1.0.0';
        $this->author = 'Modules4Presta';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '1.7.6.0', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->displayName = $this->trans('Min and max price', [], 'Modules.M4pminmaxprice.Admin');
        $this->description = $this->trans('Keeps the price of a product between a minimum and a maximum you set.', [], 'Modules.M4pminmaxprice.Admin');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayAdminProductsExtra')
            && $this->registerHook('actionProductUpdate')
            && $this->registerHook('actionProductPriceCalculation')
            && M4pMinMaxPriceDatabase::create();
    }

    public function uninstall()
    {
        return M4pMinMaxPriceDatabase::drop()
            && parent::uninstall();
    }

    /**
     * Renders the limits table on the product edit page, one row per combination.
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        $idProduct = (int) $params['id_product'];
        $limits = $this->getLimits($idProduct);

        $rows = [[
            'id_product_attribute' => 0,
            'name' => $this->trans('Whole product', [], 'Modules.M4pminmaxprice.Admin'),
            'min' => $limits[0]['min'] ?? '',
            'max' => $limits[0]['max'] ?? '',
        ]];

        foreach (Product::getProductAttributesIds($idProduct) ?: [] as $attribute) {
            $idProductAttribute = (int) $attribute['id_product_attribute'];
            $rows[] = [
                'id_product_attribute' => $idProductAttribute,
                'name' => Product::getProductName($idProduct, $idProductAttribute),
                'min' => $limits[$idProductAttribute]['min'] ?? '',
                'max' => $limits[$idProductAttribute]['max'] ?? '',
            ];
        }

        $this->context->smarty->assign([
            'm4pminmaxprice_rows' => $rows,
            'm4pminmaxprice_currency' => Validate::isLoadedObject($this->context->currency) ? $this->context->currency->iso_code : '',
        ]);

        return $this->context->smarty->fetch('module:' . $this->name . '/views/templates/admin/configuration.tpl');
    }

    public function hookActionProductUpdate($params)
    {
        $submitted = Tools::getValue('m4pminmaxprice_prices');
        if (!is_array($submitted)) {
            return;
        }

        $idProduct = (int) $params['id_product'];

        foreach ($submitted as $idProductAttribute => $limits) {
            $this->saveLimits(
                $idProduct,
                (int) $idProductAttribute,
                $this->toPrice($limits['min'] ?? null),
                $this->toPrice($limits['max'] ?? null)
            );
        }
    }

    /**
     * Clamps the calculated price to the limits of the combination, or of the
     * product when the combination has none.
     *
     * @param array $params price is passed by reference by the core
     */
    public function hookActionProductPriceCalculation(&$params)
    {
        $limits = $this->getLimitsFor((int) $params['id_product'], (int) $params['id_product_attribute']);
        if ($limits === null) {
            return;
        }

        $coefficient = $this->taxCoefficient($params);
        $price = (float) $params['price'];
        $clamped = $price;

        if ($limits['min'] !== null) {
            $clamped = max($clamped, (float) $limits['min'] * $coefficient);
        }
        if ($limits['max'] !== null) {
            $clamped = min($clamped, (float) $limits['max'] * $coefficient);
        }

        if ($clamped === $price) {
            return;
        }

        $params['price'] = $clamped;

        // The core returns this value instead of the price when only the reduction
        // is asked for, so it has to follow the clamping to stay consistent.
        if (isset($params['specific_price_reduction'])) {
            $params['specific_price_reduction'] = max(0.0, (float) $params['specific_price_reduction'] - ($clamped - $price));
        }
    }

    /**
     * @return array|null null when neither the combination nor the product has limits
     */
    private function getLimitsFor(int $idProduct, int $idProductAttribute): ?array
    {
        $limits = $this->getLimits($idProduct);

        foreach ([$idProductAttribute, 0] as $key) {
            if (isset($limits[$key]) && ($limits[$key]['min'] !== null || $limits[$key]['max'] !== null)) {
                return $limits[$key];
            }
        }

        return null;
    }

    /**
     * @return array limits indexed by id_product_attribute
     */
    private function getLimits(int $idProduct): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `id_product_attribute`, `min`, `max` FROM `' . _DB_PREFIX_ . 'm4pminmaxprice_prices`
            WHERE `id_product` = ' . $idProduct
        );

        $limits = [];
        foreach ($rows ?: [] as $row) {
            $limits[(int) $row['id_product_attribute']] = [
                'min' => $row['min'] === null ? null : (float) $row['min'],
                'max' => $row['max'] === null ? null : (float) $row['max'],
            ];
        }

        return $limits;
    }

    private function saveLimits(int $idProduct, int $idProductAttribute, ?float $min, ?float $max): bool
    {
        if ($min === null && $max === null) {
            return Db::getInstance()->delete(
                'm4pminmaxprice_prices',
                'id_product = ' . $idProduct . ' AND id_product_attribute = ' . $idProductAttribute
            );
        }

        if ($min !== null && $max !== null && $min > $max) {
            [$min, $max] = [$max, $min];
        }

        return Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'm4pminmaxprice_prices` (`id_product`, `id_product_attribute`, `min`, `max`)
            VALUES (' . $idProduct . ', ' . $idProductAttribute . ', '
            . ($min === null ? 'NULL' : (float) $min) . ', ' . ($max === null ? 'NULL' : (float) $max) . ')
            ON DUPLICATE KEY UPDATE `min` = VALUES(`min`), `max` = VALUES(`max`)'
        );
    }

    /**
     * An empty field means "no limit", so it is stored as NULL rather than zero.
     */
    private function toPrice($value): ?float
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $price = (float) str_replace(',', '.', (string) $value);

        return $price < 0 ? null : $price;
    }

    /**
     * Limits are entered tax excluded, so they need the tax rate when the core
     * asks for a tax included price.
     */
    private function taxCoefficient(array $params): float
    {
        if (empty($params['use_tax'])) {
            return 1.0;
        }

        $idAddress = isset($params['address']) && $params['address'] instanceof Address ? (int) $params['address']->id : 0;
        $rate = (float) Tax::getProductTaxRate((int) $params['id_product'], $idAddress ?: null);

        return 1 + ($rate / 100);
    }
}
