<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author  Evgeny Pervushin <pea@lab321.ru>
 */
function post_install() {
    $newConfig = array(
        'default_language' => 'ru_ru',
        'default_date_format' => 'd.m.Y',
        'default_time_format' => 'H:i',
        'default_locale_name_format' => 'f l',
        'export_delimiter' => ';',
        'default_currency_name' => 'Российский рубль',
        'default_currency_symbol' => 'руб.',
        'default_currency_iso4217' => 'RUB',
        'default_decimal_seperator' => ',',
        'default_number_grouping_seperator' => ' ',
    );
    require_once 'modules/Configurator/Configurator.php';
    $configuratorObj = new Configurator();
    $configuratorObj->loadConfig();
    foreach($newConfig as $name => $value) {
        $configuratorObj->config[$name] = $value;
    }

    $GENERATEPASSWORDTMPL_NAME = 'Шаблон письма, содержащий автоматически сгенерированный пароль';
    $tmpl = BeanFactory::newBean('EmailTemplates');
    $tmpl2 = $tmpl->retrieve_by_string_fields(array('name' => $GENERATEPASSWORDTMPL_NAME));
    if($tmpl2) {
        $tmpl = $tmpl2;
    }
    $tmpl->name = $GENERATEPASSWORDTMPL_NAME;
    $tmpl->subject = 'Данные для входа в личный кабинет';
    $tmpl->body = '
Для вас сгенерирован временный пароль:
Логин: $contact_user_user_name
Пароль: $contact_user_user_hash
Адрес сайта: $config_site_url

После того, как вы зайдете в личный кабинет, вам нужно будет задать свой пароль.
Если вы не регистрировались на нашем сайте и не запрашивали восстановление пароля, просто проигнорируйте это письмо.
    ';
    $tmpl->body_html = '<div><table width="550"><tbody><tr><td>
<p>Для вас сгенерирован временный пароль:</p>
<p>Логин: $contact_user_user_name</p>
<p>Пароль: $contact_user_user_hash</p>
<p>Адрес сайта: $config_site_url</p>
<br />
<p>После того, как вы зайдете в личный кабинет, вам нужно будет задать свой пароль.</p>
<p>Если вы не регистрировались на нашем сайте и не запрашивали восстановление пароля, просто проигнорируйте это письмо.</p>
</td>         </tr><tr><td></td>         </tr></tbody></table></div>';
    $tmpl->save();
    $configuratorObj->config['passwordsetting']['generatepasswordtmpl'] = $tmpl->id;

    $LOSTPASSWORDTMPL_NAME = 'Шаблон письма, содержащий автоматически сгенерированную ссылку сброса пароля';
    $tmpl = BeanFactory::newBean('EmailTemplates');
    $tmpl2 = $tmpl->retrieve_by_string_fields(array('name' => $LOSTPASSWORDTMPL_NAME));
    if($tmpl2) {
        $tmpl = $tmpl2;
    }
    $tmpl->name = $LOSTPASSWORDTMPL_NAME;
    $tmpl->subject = 'Восстановление пароля';
    $tmpl->body = '
$contact_user_pwd_last_changed вы запросили восстановление пароля.

Перейдите по ссылке ниже, чтобы сбросить пароль:

$contact_user_link_guid
';
    $tmpl->body_html = '<div><table width="550"><tbody><tr><td>
<p>$contact_user_pwd_last_changed вы запросили восстановление пароля. </p>
<p>Перейдите по ссылке ниже, чтобы сбросить пароль:</p>
<p> $contact_user_link_guid </p>
</td>         </tr><tr><td></td>         </tr></tbody></table></div>';
    $tmpl->save();
    $configuratorObj->config['passwordsetting']['lostpasswordtmpl'] = $tmpl->id;

    $configuratorObj->saveConfig();
}
