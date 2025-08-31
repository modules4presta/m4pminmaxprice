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

class Database
{
    private static array $sql = [];

    /**
     * Create tables
     * 
     * @return bool
     */
    public static function create(): bool
    {
        try {        
            self::$sql[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "m4pminmaxprice_prices` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_product` INT(10) UNSIGNED NOT NULL,
                `id_product_attribute` INT(10) UNSIGNED NULL DEFAULT NULL,
                `min` FLOAT(32) UNSIGNED NULL DEFAULT NULL,
                `max` FLOAT(32) UNSIGNED NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_prices` (`id_product`, `id_product_attribute`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

            foreach (self::$sql as $sql) {
                if (\Db::getInstance()->execute($sql) === false) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Drop tables
     * 
     * @return bool
     */
    public static function drop(): bool
    {
        try {
            self::$sql[] = "DROP TABLE " . _DB_PREFIX_ . "m4pminmaxprice_prices";

            foreach (self::$sql as $sql) {
                if (\Db::getInstance()->execute($sql) === false) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
