<?php

namespace App\Helper;

use App\Dictionary\LanguageDictionary;

class TranslationHelper
{
    public function formatDataForEntity($data)
    {
        if (is_array($data)) {
            $data['translations'] = [];
            $localeIdx            = 0;

            foreach (LanguageDictionary::getLanguages() as $lang) {
                if (isset($data[$lang])) {
                    foreach ($data[$lang] as $fieldName => $fieldValue) {
                        $data['translations'][$lang]['locale']   = $lang;
                        $data['translations'][$lang][$fieldName] = $fieldValue;
                    }

                    unset($data[$lang]);
                } else {
                    $data['translations'][$lang]['locale'] = $lang;
                    $data['translations'][$lang]['']       = '';
                }
                $localeIdx++;
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function normalize(array $data): array
    {
        if (isset($data['translations'])) {
            $translations = $data['translations'];
            unset($data['translations']);

            foreach ($translations as $translation) {
                if (isset($translation['locale'])) {
                    $locale = $translation['locale'];
                    unset($translation['locale']);
                    $data[$locale] = $translation;
                }
            }
        }

        return $data;
    }
}