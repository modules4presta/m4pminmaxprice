{**
 * m4pminmaxprice
 *
 * @author    Modules4Presta <contact@modules4presta.io>
 * @copyright 2026 Nice Code sp. z o.o. (Modules4Presta)
 * @license   https://opensource.org/licenses/MIT MIT License
 *}

<div class="panel">
    <h3>{l s='Min and max price' d='Modules.M4pminmaxprice.Admin'}</h3>
    <p class="help-block">
        {l s='Prices are tax excluded. Leave a field empty for no limit.' d='Modules.M4pminmaxprice.Admin'}
    </p>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>{l s='Combination' d='Modules.M4pminmaxprice.Admin'}</th>
            <th>{l s='Minimum price' d='Modules.M4pminmaxprice.Admin'}{if $m4pminmaxprice_currency} ({$m4pminmaxprice_currency|escape:'html':'UTF-8'}){/if}</th>
            <th>{l s='Maximum price' d='Modules.M4pminmaxprice.Admin'}{if $m4pminmaxprice_currency} ({$m4pminmaxprice_currency|escape:'html':'UTF-8'}){/if}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $m4pminmaxprice_rows as $row}
            <tr>
                <td>{$row.name|escape:'html':'UTF-8'}</td>
                <td>
                    <input type="number" step="0.01" min="0" class="form-control"
                           name="m4pminmaxprice_prices[{$row.id_product_attribute|intval}][min]"
                           value="{$row.min|escape:'html':'UTF-8'}">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" class="form-control"
                           name="m4pminmaxprice_prices[{$row.id_product_attribute|intval}][max]"
                           value="{$row.max|escape:'html':'UTF-8'}">
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
