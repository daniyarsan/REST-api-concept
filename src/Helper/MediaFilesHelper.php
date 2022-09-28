<?php

namespace App\Helper;

use App\Dictionary\LanguageDictionary;
use App\Dictionary\MediaTypesDictionary;

class MediaFilesHelper
{
    public function formatDataForEntity($data)
    {
        if (is_array($data) && isset($data['mediaFiles'])) {
            $mediaFiles = [];
            foreach ($data['mediaFiles'] as $type => $items) {
                foreach ($items as &$item) {
                    $item['typeId'] = MediaTypesDictionary::getTypes()[$type];
                    $mediaFiles[] = $item;
                }
            }

            $data['mediaFiles'] = $mediaFiles;
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function normalize(array $data): array
    {
        if (isset($data['mediaFiles'])) {
            $mediaFiles = $data['mediaFiles'];
            unset($data['mediaFiles']);

            foreach ($mediaFiles as $mediaFile) {
                $typeId = $mediaFile['typeId'];
                unset($mediaFile['typeId']);
                $data['mediaFiles'][array_flip(MediaTypesDictionary::getTypes())[$typeId]][] = $mediaFile;
            }
        }

        return $data;
    }
}