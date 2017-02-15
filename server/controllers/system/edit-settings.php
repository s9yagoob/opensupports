<?php

class EditSettingsController extends Controller {
    const PATH = '/edit-settings';
    const METHOD = 'POST';

    public function validations() {
        return [
            'permission' => 'staff_3',
            'requestData' => []
        ];
    }

    public function handler() {
        $settings = [
            'language',
            'recaptcha-public',
            'recaptcha-private',
            'no-reply-email',
            'smtp-host',
            'smtp-port',
            'smtp-user',
            'smtp-pass',
            'time-zone',
            'maintenance-mode',
            'layout',
            'allow-attachments',
            'max-size',
            'title',
            'url'
        ];

        foreach($settings as $setting) {
            if(Controller::request($setting)) {
                $settingInstance = Setting::getSetting($setting);
                $settingInstance->value = Controller::request($setting);
                $settingInstance->store();
            }
        }

        if(Controller::request('allowedLanguages') || Controller::request('supportedLanguages')) {
            $this->handleLanguages();
        }

        Log::createLog('EDIT_SETTINGS', null);

        Response::respondSuccess();
    }
    
    public function handleLanguages() {
        $allowed = json_decode(Controller::request('allowedLanguages'));
        $supported = json_decode(Controller::request('supportedLanguages'));

        foreach(Language::LANGUAGES as $languageCode) {
            $language = Language::getDataStore($languageCode, 'code');

            $language->allowed = in_array($languageCode, $allowed);
            $language->supported = in_array($languageCode, $supported);

            $language->store();
        }

    }
}