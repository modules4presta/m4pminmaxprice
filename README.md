# M4P Min and Max Price for PrestaShop 8 & 9

**Put a floor and a ceiling under every price rule — discounts stop where your margin ends, and no combination ever sells for more than you promised.**

> **Meta description (148 chars):** Set a minimum and maximum price per product in PrestaShop. Discounts, group rules and specific prices stay inside your limits. Free MIT module.

---

## Why a price needs a floor and a ceiling

Prices in a wholesale shop come from several places at once: a group reduction, a specific price, a
catalogue rule, a volume discount. Each one looks sensible on its own, and together they can take a
product below cost.

- **Margin stays yours** — stacked discounts cannot go below the floor you set
- **Price commitments are kept** — a ceiling holds the price a customer was promised
- **Set where it belongs** — on the product, next to the price, not in a global configuration
- **Per combination** — a large pack and a single unit can have different limits

## What the module does

The module adds a table to the product edit page with one row for the product and one row per
combination. You type a minimum, a maximum, or both. PrestaShop then calculates the price the way it
always does, and the module clamps the result to your limits — in the catalogue, on the product
page, in the cart and in the order.

### Key features

- **Floor, ceiling or both** — an empty field means no limit in that direction
- **Per combination, with a fallback** — a combination without its own limits uses the ones set for
  the whole product
- **Applied after every other rule** — specific prices, group reductions and catalogue rules are
  calculated first, then clamped
- **Tax aware** — limits are entered tax excluded and converted when a tax included price is asked for
- **No configuration screen** — nothing global to set, so nothing global to get wrong

### How the limits are applied

A product priced at 100 with a 40% group reduction ends at 60. With a minimum of 75, the customer
pays 75. With no minimum and a maximum of 50, the same product is sold at 50 even though no rule
asked for it. When both fields are empty, the module does nothing.

## Compatibility

| | |
|---|---|
| PrestaShop | 1.7.6 – 9.x |
| PHP | 7.2.5+ |
| Requirements | none |
| Multistore | Limits are shared across shops |
| Themes | Works with any theme — the price is changed by PrestaShop itself |

The module performs no core overrides. It creates one table, `m4pminmaxprice_prices`, and drops it
on uninstall.

## Installation

1. Upload and install the module from **Modules → Module Manager**.
2. Open any product in **Catalogue → Products**.
3. Scroll to the **Min and max price** section and set the limits, tax excluded.
4. Save the product and check the price in the catalogue and in the cart.

## Configuration options

Everything is set per product, in the product edit page:

| Field | Description |
|---|---|
| **Minimum price** | The lowest price the product may reach after every discount. Empty means no floor. |
| **Maximum price** | The highest price the product may be sold at. Empty means no ceiling. |

The first row applies to the whole product and is used by every combination that has no limits of
its own.

## Frequently asked questions

**Are the limits gross or net?**
Net. You type the price tax excluded, and the module adds the product tax rate when PrestaShop asks
for a tax included price.

**What happens when the minimum is higher than the maximum?**
The module swaps them when saving, so the pair always makes sense.

**Does it work with specific prices and catalogue price rules?**
Yes. The module hooks into PrestaShop's own price calculation and works on its result, so every rule
is applied first and the limit has the last word.

**Does the customer see that a limit was applied?**
No. They see the final price, without a note explaining where it came from.

**What happens to the limits when I uninstall the module?**
The table is dropped, so every limit is removed. Export it first if you plan to reinstall.

---

**Keywords:** PrestaShop minimum price, maximum price, price floor, margin protection, B2B pricing,
price limits PrestaShop.

## License

MIT — see [LICENSE](LICENSE). Free to use commercially, fork and modify; keep the copyright notice.

## Contributing

Bug reports and pull requests are welcome — see [CONTRIBUTING.md](CONTRIBUTING.md). For security
issues, follow [SECURITY.md](SECURITY.md) instead of opening a public issue.

---

Built by [Nice Code](https://nice-code.com/pl/oferta/moduly-prestashop) — we build and maintain PrestaShop stores.

© Nice Code sp. z o.o. (Modules4Presta) — released under the MIT license.
