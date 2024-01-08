<?php
/**
* 2007-2016 PrestaShop
*
* Jms Testimonials
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2016 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(_PS_MODULE_DIR_.'labtestimonials/labtestimonialsInfo.php');
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class LabTestimonials extends Module implements WidgetInterface
{
    protected $_html = '';

    public function __construct()
    {
        $this->name = 'labtestimonials';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'laberthemes';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->getTranslator()->trans('LABER Testimonials', array(), 'Modules.LabTestimonials');
        $this->description = $this->getTranslator()->trans('Displays Testimonials.', array(), 'Modules.LabTestimonials');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (parent::install() && $this->registerHook('header')&& $this->registerHook('displayPosition3'))  {
            $res = true;
            $res &= Configuration::updateValue('LABTESTIMONIALS_THUMBWIDTH', 400);
            $res &= Configuration::updateValue('LABTESTIMONIALS_ITEM', 1);
            $res &= Configuration::updateValue('LABTESTIMONIALS_ALLITEMS', 6);
            $res &= Configuration::updateValue('LABTESTIMONIALS_HEIGHT', 400);
            $res &= Configuration::updateValue('LABTESTIMONIALS_IMAGE', 1);
            $res &= Configuration::updateValue('LABTESTIMONIALS_DATETIME', 1);
            $res &= Configuration::updateValue('LABTESTIMONIALS_OFFICE', 1);
            $res &= $this->createTables();
            $res &= $this->installSamples();
            return $res;
        }
        return false;
    }

    public function uninstall()
    {
        $this->_clearCache('*');

        if (parent::uninstall()) {
            $res = true;
            $res &= Configuration::deleteByName('LABTESTIMONIALS_THUMBWIDTH');
            $res &= Configuration::deleteByName('LABTESTIMONIALS_ITEM');
            $res &= Configuration::deleteByName('LABTESTIMONIALS_ALLITEMS');
            $res &= Configuration::deleteByName('LABTESTIMONIALS_HEIGHT');
            $res &= Configuration::deleteByName('LABTESTIMONIALS_IMAGE');
            $res &= Configuration::deleteByName('LABTESTIMONIALS_DATETIME');
            $res &= Configuration::deleteByName('LABTESTIMONIALS_OFFICE');
            $res = $this->deleteTables();
            return $res;
        }
        return false;
    }

    protected function createTables()
    {
        $res = (bool)Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'labtestimonials` (
                `id_testimonial` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `author` varchar(255) NOT NULL,
                `posttime` datetime NOT NULL,
                `image` varchar(255) NOT NULL,
                `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                PRIMARY KEY (`id_testimonial`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ');
        $res = (bool)Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'labtestimonials_lang` (`id_testimonial` int(10) unsigned NOT NULL AUTO_INCREMENT, `id_lang` int(10) unsigned NOT NULL, `office` varchar(255) NOT NULL, `comment` text NOT NULL, PRIMARY KEY (`id_testimonial`, `id_lang`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ');
        return $res;
    }

    protected function deleteTables()
    {
        $res1 = (bool)Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'labtestimonials`');
        $res1 = (bool)Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'labtestimonials_lang`');
        return $res1;
    }

    private function installSamples()
    {
        $languages = Language::getLanguages(false);
        $res = true;
        //add testimonials
        for ($i = 1; $i < 5; $i++) {
            $item = new LabTestimonialsInfo();
            $item->active = 1;
            $item->author = 'laberthemes';
            $item->posttime = '2012-12-12';
            /* Sets each language fields */
            foreach ($languages as $language) {
                $item->office[$language['id_lang']] = 'CEO & Founder DooTr';
                $item->comment[$language['id_lang']] = 'Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit.';
                $item->image = 'img'.$i.'.jpg';
            }
            $res &= $item->add();
        }

        return $res;
    }

    public function getContent()
    {
        $this->_html .= $this->renderPathway();
        $this->_html .= $this->headerHTML();
        if (Tools::isSubmit('submitConfig') || Tools::isSubmit('submitInfo') || Tools::isSubmit('delete_id_tes') || Tools::isSubmit('status_id_testimonial')) {
            if ($this->_postValidation()) {
                $this->_postProcess();
                $this->_html .= $this->renderTestimonialsList();
            }

            $this->clearCache();
        } elseif (Tools::isSubmit('addTes') || Tools::isSubmit('id_testimonial')) {
            $this->_html .= $this->renderForm();
        } else {
            $view = Tools::getValue('view', 'items');
            if ($view == 'items') {
                $this->_html .= $this->renderTestimonialsList();
            }
            if ($view == 'config') {
                $this->_html .= $this->renderConf();
            }
        }
        return $this->_html;
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->getTranslator()->trans('Item informations', array(), 'Modules.LabTestimonials'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type'  => 'text',
                        'label' => $this->getTranslator()->trans('Author', array(), 'Modules.LabTestimonials'),
                        'name'  => 'author',
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->getTranslator()->trans('Office', array(), 'Modules.LabTestimonials'),
                        'name'  => 'office',
                        'lang'  => true,
                    ),
                    array(
                        'type'         => 'textarea',
                        'label'        => $this->getTranslator()->trans('Comment', array(), 'Modules.LabTestimonials'),
                        'name'         => 'comment',
                        'autoload_rte' => true,
                        'lang'         => true,
                    ),
                    array(
                        'type'  => 'datetime',
                        'label' => $this->getTranslator()->trans('Date Time', array(), 'Modules.LabTestimonials'),
                        'name'  => 'posttime',
                    ),
                    array(
                        'type'  => 'file_lang',
                        'label' => $this->getTranslator()->trans('Image', array(), 'Admin.Global'),
                        'name'  => 'image',
                        'lang'  => false,
                        'desc'  => sprintf($this->getTranslator()->trans('Maximum image size: %s.', array(), 'Admin.Global'), ini_get('upload_max_filesize'))
                    ),
                    array(
                        'type'    => 'switch',
                        'label'   => $this->getTranslator()->trans('Enable', array(), 'Admin.Global'),
                        'name'    => 'active',
                        'is_bool' => true,
                        'values'  => array(
                            array(
                                'id'    => 'active_on',
                                'value' => 1,
                                'label' => $this->getTranslator()->trans('Yes', array(), 'Admin.Global')
                            ),
                            array(
                                'id'    => 'active_off',
                                'value' => 0,
                                'label' => $this->getTranslator()->trans('No', array(), 'Admin.Global')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->getTranslator()->trans('Save', array(), 'Admin.Actions'),
                )
            ),
        );
        if (Tools::isSubmit('id_testimonial') && $this->testimonialsExists((int)Tools::getValue('id_testimonial'))) {
            $Testimonials = new LabTestimonialsInfo((int)Tools::getValue('id_testimonial'));
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_testimonial');
            if (count($Testimonials->image) > 0) {
                $fields_form['form']['images'] = $Testimonials->image;
            }
            $has_picture = true;

            foreach (Language::getLanguages(false) as $lang) {
                if (!isset($Testimonials->image)) {
                    $has_picture &= false;
                }
            }
            if ($has_picture) {
                $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'has_picture');
            }
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitInfo';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang'  => $language->id,
                'iso_code' => $language->iso_code
            ),
            'fields_value'  => $this->getFieldsValues(),
            'languages'     => $this->context->controller->getLanguages(),
            'id_language'   => $this->context->language->id,
            'image_baseurl' => $this->_path.'views/img/'
        );
        $helper->override_folder = '/';
        return $helper->generateForm(array($fields_form));
    }

    public function getFieldsValues()
    {
        $fields = array();

        if (Tools::isSubmit('id_testimonial')) {
            $Testimonials = new LabTestimonialsInfo((int)Tools::getValue('id_testimonial'));
            $fields['id_testimonial'] = (int)Tools::getValue('id_testimonial', $Testimonials->id);
        } else {
            $Testimonials = new LabTestimonialsInfo();
        }

        $fields['active']      = Tools::getValue('active', $Testimonials->active);
        $fields['posttime']    = Tools::getValue('posttime', $Testimonials->posttime);
        $fields['author']      = Tools::getValue('author', $Testimonials->author);
        $fields['image']       = Tools::getValue('image', $Testimonials->image);
        $fields['has_picture'] = true;

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $fields['office'][$lang['id_lang']]  = Tools::getValue('office_'.(int)$lang['id_lang'], $Testimonials->office[$lang['id_lang']]);
            $fields['comment'][$lang['id_lang']] = Tools::getValue('comment_'.(int)$lang['id_lang'], $Testimonials->comment[$lang['id_lang']]);
        }
        return $fields;
    }

    public function renderConf()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->getTranslator()->trans('Settings', array(), 'Admin.Global'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->getTranslator()->trans('Show Image', array(), 'Module.LabTestimonials'),
                        'name' => 'LABTESTIMONIALS_IMAGE',
                        'values' => array(
                            array(
                                'id'    => 'active_on',
                                'value' => 1,
                                'label' => $this->getTranslator()->trans('Yes', array(), 'Admin.Global')
                            ),
                            array(
                                'id'    => 'active_off',
                                'value' => 0,
                                'label' => $this->getTranslator()->trans('No', array(), 'Admin.Global')
                            )
                        ),
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->getTranslator()->trans('Thumb Width', array(), 'Module.LabTestimonials'),
                        'name'  => 'LABTESTIMONIALS_THUMBWIDTH'
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->getTranslator()->trans('Thumb Height', array(), 'Module.LabTestimonials'),
                        'name'  => 'LABTESTIMONIALS_HEIGHT'
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->getTranslator()->trans('All Items', array(), 'Module.LabTestimonials'),
                        'name'  => 'LABTESTIMONIALS_ALLITEMS'
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->getTranslator()->trans('Item Show', array(), 'Module.LabTestimonials'),
                        'name'  => 'LABTESTIMONIALS_ITEM'
                    ),
                    array(
                        'type'  => 'switch',
                        'label' => $this->getTranslator()->trans('Show DateTime', array(), 'Module.LabTestimonials'),
                        'name'  => 'LABTESTIMONIALS_DATETIME',
                        'values' => array(
                            array(
                                'id'    => 'active_on',
                                'value' => 1,
                                'label' => $this->getTranslator()->trans('Yes', array(), 'Admin.Global')
                            ),
                            array(
                                'id'    => 'active_off',
                                'value' => 0,
                                'label' => $this->getTranslator()->trans('No', array(), 'Admin.Global')
                            )
                        ),
                    ),
                    array(
                        'type'  => 'switch',
                        'label' => $this->getTranslator()->trans('Show Office', array(), 'Module.LabTestimonials'),
                        'name'  => 'LABTESTIMONIALS_OFFICE',
                        'values' => array(
                            array(
                                'id'    => 'active_on',
                                'value' => 1,
                                'label' => $this->getTranslator()->trans('Yes', array(), 'Admin.Global')
                            ),
                            array(
                                'id'    => 'active_off',
                                'value' => 0,
                                'label' => $this->getTranslator()->trans('No', array(), 'Admin.Global')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->getTranslator()->trans('Save', array(), 'Admin.Actions'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitConfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'LABTESTIMONIALS_THUMBWIDTH' => Tools::getValue('LABTESTIMONIALS_THUMBWIDTH', Configuration::get('LABTESTIMONIALS_THUMBWIDTH')),
            'LABTESTIMONIALS_HEIGHT'     => Tools::getValue('LABTESTIMONIALS_HEIGHT', Configuration::get('LABTESTIMONIALS_HEIGHT')),
            'LABTESTIMONIALS_IMAGE'      => Tools::getValue('LABTESTIMONIALS_IMAGE', Configuration::get('LABTESTIMONIALS_IMAGE')),
            'LABTESTIMONIALS_DATETIME'   => Tools::getValue('LABTESTIMONIALS_DATETIME', Configuration::get('LABTESTIMONIALS_DATETIME')),
            'LABTESTIMONIALS_OFFICE'     => Tools::getValue('LABTESTIMONIALS_OFFICE', Configuration::get('LABTESTIMONIALS_OFFICE')),
            'LABTESTIMONIALS_ALLITEMS'   => Tools::getValue('LABTESTIMONIALS_ALLITEMS', Configuration::get('LABTESTIMONIALS_ALLITEMS')),
            'LABTESTIMONIALS_ITEM'       => Tools::getValue('LABTESTIMONIALS_ITEM', Configuration::get('LABTESTIMONIALS_ITEM'))
        );
    }

    public function renderTestimonialsList()
    {
        $testimonials = $this->getlistprove();
        foreach ($testimonials as $key => $testimonialss) {
            $testimonials[$key]['status'] = $this->displayStatus($testimonialss['id_testimonial'], $testimonialss['active']);
        }
        $this->context->smarty->assign(
            array(
                'link'         => $this->context->link,
                'testimonials' => $testimonials,
                'image_url'    =>$this->_path.'views/img/'
            )
        );
        return $this->display(__FILE__, 'labtestimonialslist.tpl');
    }



    private function _postValidation()
    {
        $errors = array();
        if (Tools::isSubmit('status_id_testimonial')) {
            if (!Validate::isInt(Tools::getValue('id_testimonial'))) {
                $errors[] = $this->getTranslator()->trans('Invalid Item', array(), 'Modules.LabTestimonials');
            }
        } elseif (Tools::isSubmit('submitInfo')) {
            /* If edit : checks id_item */
            if (Tools::isSubmit('id_testimonial')) {
                if (!Validate::isInt(Tools::getValue('id_testimonial')) && !$this->testimonialsExists(Tools::getValue('id_testimonial'))) {
                    $errors[] = $this->getTranslator()->trans('Invalid id_item');
                }
            }

            $languages = Language::getLanguages(false);

            foreach ($languages as $language) {
                if (Tools::strlen(Tools::getValue('author')) > 255) {
                    $errors[] = $this->getTranslator()->trans('The author is too long.', array(), 'Modules.LabTestimonials');
                }
                if (Tools::strlen(Tools::getValue('office_'.$language['id_lang'])) > 255) {
                    $errors[] = $this->getTranslator()->trans('The office is too long.', array(), 'Modules.LabTestimonials');
                }
                if (Tools::strlen(Tools::getValue('comment'.$language['id_lang'])) > 6000) {
                    $errors[] = $this->getTranslator()->trans('The comment is too long.', array(), 'Modules.LabTestimonials');
                }
                if (Tools::getValue('image_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_'.$language['id_lang']))) {
                    $errors[] = $this->getTranslator()->trans('Invalid filename', array(), 'Modules.LabTestimonials');
                }
                if (Tools::getValue('image_old_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_old_'.$language['id_lang']))) {
                    $errors[] = $this->getTranslator()->trans('Invalid filename', array(), 'Modules.LabTestimonials');
                }
            }

            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');

            if (Tools::strlen(Tools::getValue('author')) == 0) {
                $errors[] = $this->getTranslator()->trans('The author is not set.', array(), 'Modules.LabTestimonials');
            }

            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');

            if (Tools::strlen(Tools::getValue('office_'.$id_lang_default)) == 0) {
                $errors[] = $this->getTranslator()->trans('The office is not set.', array(), 'Modules.LabTestimonials');
            }
            if (Tools::getValue('posttime') == null) {
                $errors[] = $this->getTranslator()->trans('The DateTime is not set.', array(), 'Modules.LabTestimonials');
            }
        } elseif (Tools::isSubmit('delete_id_tes') && (!Validate::isInt(Tools::getValue('delete_id_tes')) || !$this->testimonialsExists((int)Tools::getValue('delete_id_tes')))) {
            $errors[] = $this->getTranslator()->trans('Invalid id_item', array(), 'Modules.LabTestimonials');
        }

        if (count($errors)) {
            $this->_html .= $this->displayError(implode('<br />', $errors));
            $this->_html .= $this->renderForm();
            return false;
        }
        return true;
    }

    private function _postProcess()
    {
        $errors = array();
        if (Tools::isSubmit('submitConfig')) {
            $res = Configuration::updateValue('LABTESTIMONIALS_IMAGE', (int)(Tools::getValue('LABTESTIMONIALS_IMAGE')));
            $res &= Configuration::updateValue('LABTESTIMONIALS_DATETIME', (int)(Tools::getValue('LABTESTIMONIALS_DATETIME')));
            $res &= Configuration::updateValue('LABTESTIMONIALS_HEIGHT', (int)(Tools::getValue('LABTESTIMONIALS_HEIGHT')));
            $res &= Configuration::updateValue('LABTESTIMONIALS_OFFICE', (int)(Tools::getValue('LABTESTIMONIALS_OFFICE')));
            $res &= Configuration::updateValue('LABTESTIMONIALS_ITEM', (int)(Tools::getValue('LABTESTIMONIALS_ITEM')));
            $res &= Configuration::updateValue('LABTESTIMONIALS_ALLITEMS', (int)(Tools::getValue('LABTESTIMONIALS_ALLITEMS')));
            $res &= Configuration::updateValue('LABTESTIMONIALS_THUMBWIDTH', (int)(Tools::getValue('LABTESTIMONIALS_THUMBWIDTH')));
            $res &= Configuration::updateValue('JMSWEEKMENU_THUMBHEIGHT', (int)(Tools::getValue('JMSWEEKMENU_THUMBHEIGHT')));

            $this->clearCache();
            if (!$res) {
                $errors[] = $this->displayError($this->getTranslator()->trans('The configuration could not be updated.', array(), 'Modules.LabTestimonials'));
            } else {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=6&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=config');
            }
        } elseif (Tools::isSubmit('submitInfo')) {
            if (Tools::getValue('id_testimonial')) {
                $tes = new LabTestimonialsInfo((int)Tools::getValue('id_testimonial'));

                if (!Validate::isLoadedObject($tes)) {
                    $this->_html .= $this->displayError($this->getTranslator()->trans('Invalid id_testimonial', array(), 'Modules.LabTestimonials'));
                    return;
                }
            } else {
                $tes = new LabTestimonialsInfo();
            }
            /* Sets active */
            $tes->active = (int)Tools::getValue('active');
            $tes->posttime = Tools::getValue('posttime');
            $tes->author = Tools::getValue('author');
            /* Uploads image and sets testimonials */
            $type = Tools::strtolower(Tools::substr(strrchr($_FILES['image']['name'], '.'), 1));
            $imagesize = array();
            $imagesize = @getimagesize($_FILES['image']['tmp_name']);
            if (isset($_FILES['image']) && isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name']) && !empty($imagesize) && in_array(Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)), array('jpg', 'gif', 'jpeg', 'png')) && in_array($type, array('jpg', 'gif', 'jpeg', 'png'))) {
                $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                $salt = sha1(microtime());

                if ($error = ImageManager::validateUpload($_FILES['image'])) {
                    $errors[] = $error;
                } elseif (!$temp_name || !move_uploaded_file($_FILES['image']['tmp_name'], $temp_name)) {
                    return false;
                } elseif (!ImageManager::resize($temp_name, dirname(__FILE__).'/views/img/'.Tools::encrypt($_FILES['image']['name'].$salt).'.'.$type, null, null, $type)) {
                    $errors[] = $this->displayError($this->getTranslator()->trans('An error occurred during the image upload process.', array(), 'Modules.LabTestimonials'));
                }

                if (isset($temp_name)) {
                    @unlink($temp_name);
                }

                $tes->image = Tools::encrypt($_FILES['image']['name'].$salt).'.'.$type;

                $this->createThumb(dirname(__FILE__).'/views/img/', Tools::encrypt($_FILES['image']['name'].$salt).'.'.$type, Configuration::get('JMS_IMAGEGALLERY_LIGHTBOX_HEIGHT'), Configuration::get('JMS_IMAGEGALLERY_LIGHTBOX_WIDTH'), 'resized_', 0);

                //delete old img
                $old_img = Tools::getValue('image_old');

                if ($old_img && file_exists(dirname(__FILE__).'/views/img/'.$old_img)) {
                    @unlink(dirname(__FILE__).'/views/img/'.$old_img);
                    @unlink(dirname(__FILE__).'/views/img/resized_'.$old_img);
                }
            } elseif (Tools::getValue('image_old') != '') {
                $tes->image = Tools::getValue('image_old');
            }
            /* Sets each langue fields */
            $languages = Language::getLanguages(false);

            foreach ($languages as $language) {
                $tes->office[$language['id_lang']] = Tools::getValue('office_'.$language['id_lang']);
                $tes->comment[$language['id_lang']] = strip_tags(Tools::getValue('comment_'.$language['id_lang']));
            }

            /* Processes if no errors  */
            if (!$errors) {
                if (!Tools::getValue('id_testimonial')) {
                    /* Adds */
                    if (!$tes->add()) {
                        $errors[] = $this->displayError($this->getTranslator()->trans('The Information could not be added.', array(), 'Modules.LabTestimonials'));
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=3&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=items');
                    }
                } elseif (!$tes->update()) {
                    $errors[] = $this->displayError($this->getTranslator()->trans('The Information could not be updated.', array(), 'Modules.LabTestimonials'));
                    $this->clearCache();
                } elseif (Tools::isSubmit('submitInfo') && Tools::getValue('id_testimonial')) {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=items');
                }
            }
        } elseif (Tools::isSubmit('status_id_testimonial')) {
            $this->changeStatus(Tools::getValue('status_id_testimonial'));
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=5&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=items');
        } elseif (Tools::isSubmit('delete_id_tes')) {
            $tes = new LabTestimonialsInfo((int)Tools::getValue('delete_id_tes'));
            $res = $tes->delete();
            $this->clearCache();
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=items');
        }
        $this->clearCache();
    }

    public function createThumb($src, $image, $max_height, $max_width, $resize_name, $crop)
    {
        if ($image) {
            if ($crop) {
                $imgInfo = getimagesize($src.'/'.$image);
                $width = $imgInfo[0];
                $height = $imgInfo[1];
                $ratio = max($max_width / $width, $max_height / $height);
                $y = ($height - $max_height / $ratio) / 2;
                $height = $max_height / $ratio;
                $x = ($width - $max_width / $ratio) / 2;
                $width = $max_width / $ratio;
                $rzname = $resize_name.$image; // get the file extension
                $resized = $src.'/'.$rzname;

                switch ($imgInfo[2]) {
                    case 1:
                        $im = imagecreatefromgif($src.'/'.$image);
                        break;
                    case 2:
                        $im = imagecreatefromjpeg($src.'/'.$image);
                        break;
                    case 3:
                        $im = imagecreatefrompng($src.'/'.$image);
                        break;
                    default:
                        return '';
                }
                $newImg = imagecreatetruecolor($max_height, $max_height);

                /* Check if this image is PNG or GIF, then set if Transparent*/
                if (($imgInfo[2] == 1) || ($imgInfo[2] == 3)) {
                    imagealphablending($newImg, false);
                    imagesavealpha($newImg, true);
                    $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
                    imagefilledrectangle($newImg, 0, 0, $max_width, $max_height, $transparent);
                }
                imagecopyresampled($newImg, $im, 0, 0, $x, $y, $max_width, $max_height, $width, $height);

                //Generate the file, and rename it to $newfilename
                switch ($imgInfo[2]) {
                    case 1:
                        imagegif($newImg, $resized);
                        break;
                    case 2:
                        imagejpeg($newImg, $resized, 90);
                        break;
                    case 3:
                        imagepng($newImg, $resized);
                        break;
                    default:
                        return '';
                }
                return $src.'/'.$rzname;
            } else {
                $imgInfo = getimagesize($src.'/'.$image);
                $width = $imgInfo[0];
                $height = $imgInfo[1];
                if (!$max_width && !$max_height) {
                    $max_width = $width;
                    $max_height = $height;
                } else {
                    if (!$max_width) {
                        $max_width = 1000;
                    }
                    if (!$max_height) {
                        $max_height = 1000;
                    }
                }
                $x_ratio = $max_width / $width;
                $y_ratio = $max_height / $height;

                if (($width <= $max_width) && ($height <= $max_height)) {
                    $tn_width = $width;
                    $tn_height = $height;
                } else if (($x_ratio * $height) < $max_height) {
                    $tn_height = ceil($x_ratio * $height);
                    $tn_width = $max_width;
                } else {
                    $tn_width = ceil($y_ratio * $width);
                    $tn_height = $max_height;
                }

                $rzname = $resize_name.$image; // get the file extension

                $resized = $src.'/'.$rzname;

                switch ($imgInfo[2]) {
                    case 1:
                        $im = imagecreatefromgif($src.'/'.$image);
                        break;
                    case 2:
                        $im = imagecreatefromjpeg($src.'/'.$image);
                        break;
                    case 3:
                        $im = imagecreatefrompng($src.'/'.$image);
                        break;
                    default:
                        return '';
                }

                $newImg = imagecreatetruecolor($tn_width, $tn_height);

                /* Check if this image is PNG or GIF, then set if Transparent*/
                if (($imgInfo[2] == 1) || ($imgInfo[2] == 3)) {
                    imagealphablending($newImg, false);
                    imagesavealpha($newImg, true);
                    $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
                    imagefilledrectangle($newImg, 0, 0, $tn_width, $tn_height, $transparent);
                }
                imagecopyresampled($newImg, $im, 0, 0, 0, 0, $tn_width, $tn_height, $imgInfo[0], $imgInfo[1]);

                //Generate the file, and rename it to $newfilename
                switch ($imgInfo[2]) {
                    case 1:
                        imagegif($newImg, $resized);
                        break;
                    case 2:
                        imagejpeg($newImg, $resized, 90);
                        break;
                    case 3:
                        imagepng($newImg, $resized);
                        break;
                    default:
                        return '';
                }
                return $src.'/'.$rzname;
            }
        }
    }

    public function getlist($active)
    {
        $id_lang = $this->context->language->id;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'labtestimonials pb LEFT JOIN '._DB_PREFIX_.'labtestimonials_lang pbl ON (pbl.`id_testimonial`=pb.`id_testimonial`) WHERE pbl.`comment` !="" AND pb.`active` = "'.$active.'" AND pbl.`id_lang` = '.$id_lang.' LIMIT '.Configuration::get('LABTESTIMONIALS_ALLITEMS'));
    }

    public function getlistprove($state = -1)
    {
        $this->context = Context::getContext();
        $id_lang = $this->context->language->id;
        $filter = '';

        if ($state != -1) {
            $filter = ' AND hss.`active` = '.$state;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'labtestimonials  pb LEFT JOIN '._DB_PREFIX_.'labtestimonials_lang pbl ON (pb.`id_testimonial` = pbl.`id_testimonial`) WHERE pbl.id_lang = '.(int)$id_lang.$filter.' ORDER BY pbl.id_testimonial');
    }

    public function testimonialsExists($id_testimonial)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_testimonial` FROM '._DB_PREFIX_.'labtestimonials WHERE `id_testimonial` = "'.$id_testimonial.'"');
    }

    public function clearCache()
    {
        $this->_clearCache('labtestimonials.tpl');
    }

    public function displayStatus($id_product_status, $active)
    {
        $title = ((int)$active == 0 ? $this->getTranslator()->trans('Disabled', array(), 'Admin.Global') : $this->getTranslator()->trans('Enabled', array(), 'Admin.Global'));
        $icon = ((int)$active == 0 ? 'icon-remove' : 'icon-check');
        $class = ((int)$active == 0 ? 'btn-danger' : 'btn-success');
        $html = '<a class="btn '.$class.'" href="'.AdminController::$currentIndex.
            '&configure='.$this->name.'
                &token='.Tools::getAdminTokenLite('AdminModules').'
                &changeStatus&status_id_testimonial='.(int)$id_product_status.'" title="'.$title.'"><i class="'.$icon.'"></i> '.$title.'</a>';
        return $html;
    }

    public function changeStatus($id_product)
    {
        $active = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT active FROM '._DB_PREFIX_.'labtestimonials WHERE id_testimonial="'.$id_product.'"');
        $change_active = ($active[0]['active'] == 0 ? $active[0]['active'] = 1 : $active[0]['active'] = 0);
        $abc = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('UPDATE '._DB_PREFIX_.'labtestimonials  SET `active` = "'.$change_active.'" WHERE `id_testimonial` = "'.$id_product.'"');
        return $abc;
    }

    public function renderPathway()
    {
        $this->context->smarty->assign(
            array(
                'view' => Tools::getValue('view'),
                'link' => $this->context->link
            )
        );
        return $this->display(__FILE__, 'path.tpl');
    }

    public function headerHTML()
    {
        $this->context->controller->addJqueryUI('ui.sortable');
        /* Style & js for fieldset 'slides configuration' */
        $html = '<script type="text/javascript">
            $(function() {
                var $mySlides = $("#slides");
                $mySlides.sortable({
                    opacity: 0.6,
                    cursor: "move",
                    update: function() {
                        var order = $(this).sortable("serialize") + "&action=updateSlidesOrdering";
                        $.post("'.$this->context->shop->physical_uri.$this->context->shop->virtual_uri.'modules/'.$this->name.'/ajax_'.$this->name.'.php?", order);
                        }
                    });
                $mySlides.hover(function() {
                    $(this).css("cursor","move");
                    },
                    function() {
                    $(this).css("cursor","auto");
                });
            });
            $(function() {
                var $myLayers = $("#layers");
                $myLayers.sortable({
                    opacity: 0.6,
                    cursor: "move",
                    update: function() {
                        var order = $(this).sortable("serialize") + "&action=updateLayersOrdering";
                        $.post("'.$this->context->shop->physical_uri.$this->context->shop->virtual_uri.'modules/'.$this->name.'/ajax_'.$this->name.'.php?", order);
                        var temp = $(this).sortable("serialize").substring(9,200);
                        var layerids = temp.split("&layers[]=");
                        var i = 1;
                        jQuery.each(layerids, function(index, value) {
                            $("#caption_" + value).css("z-index",i);
                            i++;
                        });
                    }
                });
                $myLayers.hover(function() {
                    $(this).css("cursor","move");
                    },
                    function() {
                    $(this).css("cursor","auto");
                });
            });
        </script>';
        return $html;
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS(($this->_path) . 'views/css/style.css', 'all');
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        return $this->display(__FILE__, 'labtestimonials.tpl');
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $testimonials = $this->getlist(1);

        return [
            'url' => $this->_path,
            'show_image'   => Configuration::get('LABTESTIMONIALS_IMAGE'),
            'show_date'    => Configuration::get('LABTESTIMONIALS_DATETIME'),
            'show_office'  => Configuration::get('LABTESTIMONIALS_OFFICE'),
            'labitems'     => Configuration::get('LABTESTIMONIALS_ITEM'),
            'testimonials' => $testimonials,
            'image_url'    => $this->_path.'views/img/'
        ];
    }
}
