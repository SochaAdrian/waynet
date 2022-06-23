{**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{* Helps to solve problems like mine - I used numberOfProduct instead of numberOfProducts in get products and got 0 results :) *}
{*{$products|dump}*}

{* I know it should be in head but I need it just for this module so...*}
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

<div class="test-wrapper">
{foreach from=$products item="product"}
    <section class="products-wrapper">
        <div class="product-name">
            <h1 class="text-uppercase">{$product['category']->name[$product['id_language']]}</h1>
            <div class="buttons">
                <button class="up"> <i class="material-icons">expand_less</i></button>
                <button class="down"> <i class="material-icons">expand_more</i></button>
            </div>
        </div>
        <hr/>
        <div class="products">
            {foreach from=$product['product'] item="prod"}
                {include file="module:waynetsocha/views/templates/hook/product.tpl" product=$prod}
            {/foreach}
        </div>

        <div class="category-link">
            <a class="see-more" href="{$product['categoryLink']}">  {l s= 'więcej z tej kategorii' d='Shop.Theme.Catalog'}</a>
        </div>
        <hr/>
    </section>
{/foreach}
</div>