<?php

/**
 * Get an array of backup schedule types with their labels.
 *
 * @return array an array of backup schedule types with labels
 */
if (!function_exists('getBackupSchedulesTypes')) {
    function getBackupSchedulesTypes()
    {
        $types = [
            ''     => '',
            '1'    => app_lang('type_manual'),
            '2_H'  => app_lang('type_every_two_hours'),
            '4_H'  => app_lang('type_every_four_hours'),
            '8_H'  => app_lang('type_every_eight_hours'),
            '12_H' => app_lang('type_every_twelve_hours'),
            '1_D'  => app_lang('type_daily'),
            '7_D'  => app_lang('type_weekly'),
            '14_D' => app_lang('type_fortnightly'),
            '1_M'  => app_lang('type_monthly'),
        ];

        return $types;
    }
}

/*
 * Get an array of remote storage options with keys, labels, and icons.
 *
 * @return array An array of remote storage options with keys, labels, and icons.
 */
if (!function_exists('fetchStorageOptions')) {
    function fetchStorageOptions()
    {
        $options = [
            [
                'key'   => 'ftp',
                'label' => 'ftp_storage',
            ],
            [
                'key'   => 'sftp',
                'label' => 'sftp_storage',
            ],
            [
                'key'   => 's3',
                'label' => 's3_storage',
            ],
            [
                'key'   => 'email',
                'label' => 'email',
            ],
        ];

        return $options;
    }
}

/*
 * Get the formatted current date and time.
 *
 * @param string $datestring The optional date string to format.
 *
 * @return string The formatted current date and time.
 */
if (!function_exists('getCurrentDateTime')) {
    function getCurrentDateTime($datestring = '')
    {
        $dateString = (!empty($datestring) ? date('Y-m-d H:i:s', $datestring) : get_my_local_time());

        $dateTime = new DateTime($dateString);
        $date     = $dateTime->format(get_setting('date_format'));

        if ('24_hours' == get_setting('time_format')) {
            return $date.' '.$dateTime->format('H:i');
        }

        return $date.' '.convert_time_to_12hours_format($dateTime->format('H:i:s'));
    }
}

/*
 * Get the list of backup file options.
 *
 * @return array An array of available backup file options.
 */
if (!function_exists('getBackupFileOptions')) {
    function getBackupFileOptions()
    {
        return [
            'plugins',
            'app',
            'files',
            'assets',
            'system',
            'documentation',
            'writable',
            'other',
        ];
    }
}

/*
 * Transforms a file structure into a format suitable for JavaScript processing.
 *
 * This function takes an array of file structure data and prepares it for use in JavaScript.
 * It renames the 'text' key to 'name' and sets the 'type' to 'folder' if the 'children' key is present.
 *
 * @param array $files The input array representing the file structure.
 *
 * @return array The transformed file structure in JavaScript-friendly format.
 */
if (!function_exists('prepareFileStructureForJS')) {
    function prepareFileStructureForJS($files)
    {
        if (is_array($files)) {
            return array_map(function ($item) {
                if (isset($item['parent'])) {
                    $item['name'] = $item['parent'];
                    unset($item['parent']);
                }

                if (isset($item['child'])) {
                    $item['type'] = 'folder';
                    $item['children'] = array_map(function ($child) {
                        if (isset($child['parent'])) {
                            $child['name'] = $child['parent'];
                            unset($child['parent']);
                        }

                        return $child;
                    }, $item['child']);
                }

                return $item;
            }, $files);
        }

        return true;
    }
}
