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

class M4pMinMaxPriceDatabase
{
    public static function create(): bool
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'm4pminmaxprice_prices` (
            `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_product` INT(10) UNSIGNED NOT NULL,
            `id_product_attribute` INT(10) UNSIGNED NOT NULL DEFAULT 0,
            `min` DECIMAL(20,6) UNSIGNED NULL DEFAULT NULL,
            `max` DECIMAL(20,6) UNSIGNED NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_prices` (`id_product`, `id_product_attribute`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

        return (bool) Db::getInstance()->execute($sql);
    }

    public static function drop(): bool
    {
        return (bool) Db::getInstance()->execute(
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'm4pminmaxprice_prices`'
        );
    }
}
