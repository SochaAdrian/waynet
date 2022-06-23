<?php
/**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Waynetsocha extends Module
{
    protected $config_form = false;


    public function __construct()
    {
        $this->name = 'waynetsocha';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Adrian Socha <@adrian.soc98@gmail.com> ';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Waynet - Zadanie Rekrutacyjen');
        $this->description = $this->l('Moduł stworzony przez Adrian Socha <@adrian.soc98@gmail.com> ');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->templateFile = 'module:waynetsocha/views/templates/hook/template.tpl';
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('numberOfProducts', 10);
        Configuration::updateValue('categoryOne', 1);
        Configuration::updateValue('categoryTwo', 2);
        Configuration::updateValue('categoryThree', 3);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        Configuration::deleteByName('WAYNETSOCHA_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        $output = '';
        $errors = array();

        if (Tools::isSubmit('submitWaynetsochaModule')) {
            $numberOfProducts = Tools::getValue('numberOfProducts');
            if (!Validate::isInt($numberOfProducts) || $numberOfProducts < 1) {
                $errors[] = $this->l('Number of products must be an integer greater than 0.');
            }

            $categoryOne = Tools::getValue('categoryOne');
            if (!Validate::isInt($categoryOne) || $categoryOne < 1) {
                $errors[] = $this->l('Category one must be an integer greater than 0.');
            }
            $categoryTwo = Tools::getValue('categoryTwo');
            if (!Validate::isInt($categoryTwo) || $categoryTwo < 1) {
                $errors[] = $this->l('Category two must be an integer greater than 0.');
            }
            $categoryThree = Tools::getValue('categoryThree');
            if (!Validate::isInt($categoryThree) || $categoryThree < 1) {
                $errors[] = $this->l('Category three must be an integer greater than 0.');
            }
            if (isset($errors) && count($errors)) {
                $output = $this->displayError(implode('<br />', $errors));
            } else {
                Configuration::updateValue('numberOfProducts', $numberOfProducts);
                Configuration::updateValue('categoryOne', $categoryOne);
                Configuration::updateValue('categoryTwo', $categoryTwo);
                Configuration::updateValue('categoryThree', $categoryThree);

                $this->_clearCache('*');

                $output = $this->displayConfirmation($this->l('Settings have been updated'));
            }
        }

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {

        $form_fields = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Number of products'),
                        'name' => 'numberOfProducts',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Number of products to display in the home page.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Category one'),
                        'name' => 'categoryOne',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Category one to display in the home page.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Category two'),
                        'name' => 'categoryTwo',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Category two to display in the home page.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Category three'),
                        'name' => 'categoryThree',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Category three to display in the home page.'),
                    ),
                ),
        'submit' => array(
            'title' => $this->l('Save'),
        ),
            ),
        );

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitWaynetsochaModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($form_fields));
    }

    /**
     * Create the structure of your form.
     */
    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'numberOfProducts' => Tools::getValue('numberOfProducts'),
            'categoryOne' => Tools::getValue('categoryOne'),
            'categoryTwo' => Tools::getValue('categoryTwo'),
            'categoryThree' => Tools::getValue('categoryThree'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function getProductsFromAllCategories(){
        $category_one = (int) Configuration::get('categoryOne');
        $category_two = (int) Configuration::get('categoryTwo');
        $category_three = (int) Configuration::get('categoryThree');
        $arrayofcategoryids = [$category_one, $category_two, $category_three];

        $products = [];

        foreach ($arrayofcategoryids as $id) {

            $category = new Category($id);

            $products[$id]['category'] = $category; // Add the category to the array
            $products[$id]['categoryLink'] = Context::getContext()->link->getCategoryLink($id); // Add the category link to the array
            $products[$id]['product'] = $this->getProducts($category); // Add the products to the array
            $products[$id]['id_language'] = $this->context->language->id; // Add the language to the array (context reasons)
        }

        return $products;
    }

    public function getHookVariables($hook, array $configuration){
        $products = $this->getProductsFromAllCategories();

            if(!empty($products)){
                return array(
                    'products' => $products,
                );
            }

            return false;
    }

    protected function getProducts($category)
    {
        $numberOfProduct = Configuration::get('numberOfProducts');

        $searchProvider = new CategoryProductSearchProvider($this->context->getTranslator(), $category);
        $context = new ProductSearchContext($this->context);

        $query = new ProductSearchQuery();
        $query->setResultsPerPage($numberOfProduct)->setPage(1);

        $result = $searchProvider->runQuery(
            $context,
            $query
        );

        $assembler = new ProductAssembler($this->context);
        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );

        $tpl_products = [];

        foreach ($result->getProducts() as $rawProduct) {
            $tpl_products[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }

        return $tpl_products;
    }

    public function hookDisplayHome($hookName= null, array $configuration = []){
        $variables = $this->getHookVariables($hookName, $configuration);

        if (empty($variables)){
            return false;
        }

        $this->smarty->assign($variables);
        return $this->fetch($this->templateFile);
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }
}
