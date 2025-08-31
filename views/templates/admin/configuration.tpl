<div class="panel">
    <h3>{l s='Min and max prices for product' mod='m4pminmaxprice'}</h3>
    <input type="hidden" name="id_product" value="{$id_product}" />
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>{l s='Combination' mod='m4pminmaxprice'}</th>
            <th>{l s='Minimal price' mod='m4pminmaxprice'}</th>
            <th>{l s='Maximum price' mod='m4pminmaxprice'}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $prices as $price}
            <tr>
                <td>{$price.name|escape:'html':'UTF-8'}</td>
                <td>
                    <input type="number"
                            name="m4pminmaxprice_prices[{$price.id_product_attribute}][min]]"
                            value="{$price.min|default:0}"
                            min="0" class="form-control">
                </td>
                <td>
                    <input type="number"
                            name="m4pminmaxprice_prices[{$price.id_product_attribute}][max]"
                            value="{$price.max|default:0}"
                            min="0" class="form-control">
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
