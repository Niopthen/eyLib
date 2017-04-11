<?php

/*
 *
 * ===============================================
 * Name: func.browser_lang.php
 * ===============================================
 * Description:
 * get the language from the browser if possible
 *
 * ===============================================
 */

/**
 * get the browser language
 *
 * @param array $allowed_languages
 * @param string $default_language
 * @param string $lang_variable
 * @param string $strict_mode
 * @return string
 */
function browser_language($allowed_languages, $default_language, $lang_variable = null, $strict_mode = true)
{

    if ($lang_variable === null)
    {
        $lang_variable = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    }

    // was any information about the language send to the function?
    if (empty($lang_variable))
    {
        // No? => take the default speech send to the function
        return $default_language;
    }

    // cut the header information
    $accepted_languages = preg_split('/,\s*/', $lang_variable);

    // set the default values
    $current_lang = $default_language;
    $current_q = 0;

    // check all given languages
    foreach ($accepted_languages as $accepted_language)
    {
        // get all information about the speech
        $res = preg_match('/^([a-z]{1,8}(?:-[a-z]{1,8})*)' .
                '(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i', $accepted_language, $matches);

        // was the syntax correct?
        if (!$res)
        {
            // no then ignore it
            continue;
        }

        // get speech code and split it 
        $lang_code = explode('-', $matches[1]);

        // was a quality given
        if (isset($matches[2]))
        {
            // use the quality
            $lang_quality = (float) $matches[2];
        }
        else
        {
            // Quality Mode - take quality 1
            $lang_quality = 1.0;
        }

        while (count($lang_code))
        {
            if (in_array(strtolower(join('-', $lang_code)), $allowed_languages))
            {
                if ($lang_quality > $current_q)
                {
                    // diese Sprache verwenden
                    $current_lang = strtolower(join('-', $lang_code));
                    $current_q = $lang_quality;
                    // Hier die innere while-Schleife verlassen
                    break;
                }
            }
            if ($strict_mode)
            {

                break;
            }

            array_pop($lang_code);
        }
    }

    return $current_lang;
}
