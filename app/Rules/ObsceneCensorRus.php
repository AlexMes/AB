<?php

namespace App\Rules;

use App\Rules\CensorHelpers\ReflectionTypeHint;
use App\Rules\CensorHelpers\UTF8;

class ObsceneCensorRus
{
    public static function isAllowed($text, $charset = 'UTF-8')
    {
        return self::parse($text, '3', "\xe2\x80\xa6", true, null, $charset) === false;
    }

    /**
     *
     * @param string      $s        строка для проверки
     * @param string      $delta    ширина найденного фрагмента в словах
     *                              (кол-во слов от матного слова слева и справа, максимально 10)
     * @param string      $continue строка, которая будет вставлена в начале и в конце фрагмента
     * @param bool        $is_html  расценивать строку как HTML код?
     *                              в режиме $is_html === TRUE html код игнорируется, а html сущности заменяются в "чистый" UTF-8
     * @param string|null $replace  строка, на которую заменять матный фрагмент, например: '[ой]' ($replace д.б. в кодировке $charset)
     *                              опция работает в PHP >= 5.2.0
     * @param string      $charset  кодировка символов (родная кодировка -- UTF-8, для других будет прозрачное перекодирование)
     *
     * @return bool|string|int|null Если $replace === NULL, то возвращает FALSE, если мат не обнаружен, иначе фрагмент текста с матерным словом.
     *                              Если $replace !== NULL, то возвращает исходную строку, где фрагменты мата заменены на $replace.
     *                              В случае возникновения ошибки возвращает код ошибки > 0 (integer):
     *                              * PREG_INTERNAL_ERROR
     *                              * PREG_BACKTRACK_LIMIT_ERROR (see also pcre.backtrack_limit)
     *                              * PREG_RECURSION_LIMIT_ERROR (see also pcre.recursion_limit)
     *                              * PREG_BAD_UTF8_ERROR
     *                              * PREG_BAD_UTF8_OFFSET_ERROR (since PHP 5.3.0)
     *                              Или -1, если ReflectionTypeHint вернул ошибку
     */
    public static function parse(
        $s,
        $delta = 3,
        $continue = "\xe2\x80\xa6",
        $is_html = true,
        $replace = null,
        $charset = 'UTF-8'
    ) {
        if (! ReflectionTypeHint::isValid()) {
            return -1;
        }
        if ($s === null) {
            return null;
        }

        static $re_badwords = null;

        if ($re_badwords === null) {
            $pretext = [

                '[уyоoаa]_?      (?=[еёeхx])',
                '[вvbсc]_?       (?=[хпбмгжxpmgj])',
                '[вvbсc]_?[ъь]_? (?=[еёe])',
                'ё_?             (?=[бb6])',

                '[вvb]_?[ыi]_?',
                '[зz3]_?[аa]_?',
                '[нnh]_?[аaеeиi]_?',
                '[вvb]_?[сc]_?          (?=[хпбмгжxpmgj])',
                '[оo]_?[тtбb6]_?        (?=[хпбмгжxpmgj])',
                '[оo]_?[тtбb6]_?[ъь]_?  (?=[еёe])',
                '[иiвvb]_?[зz3]_?       (?=[хпбмгжxpmgj])',
                '[иiвvb]_?[зz3]_?[ъь]_? (?=[еёe])',
                '[иi]_?[сc]_?           (?=[хпбмгжxpmgj])',
                '[пpдdg]_?[оo]_? (?> [бb6]_?         (?=[хпбмгжxpmgj])
                               | [бb6]_?  [ъь]_? (?=[еёe])
                               | [зz3]_? [аa] _?
                             )?',

                '[пp]_?[рr]_?[оoиi]_?',
                '[зz3]_?[лl]_?[оo]_?',
                '[нnh]_?[аa]_?[дdg]_?         (?=[хпбмгжxpmgj])',
                '[нnh]_?[аa]_?[дdg]_?[ъь]_?   (?=[еёe])',
                '[пp]_?[оoаa]_?[дdg]_?        (?=[хпбмгжxpmgj])',
                '[пp]_?[оoаa]_?[дdg]_?[ъь]_?  (?=[еёe])',
                '[рr]_?[аa]_?[зz3сc]_?        (?=[хпбмгжxpmgj])',
                '[рr]_?[аa]_?[зz3сc]_?[ъь]_?  (?=[еёe])',
                '[вvb]_?[оo]_?[зz3сc]_?       (?=[хпбмгжxpmgj])',
                '[вvb]_?[оo]_?[зz3сc]_?[ъь]_? (?=[еёe])',

                '[нnh]_?[еe]_?[дdg]_?[оo]_?',
                '[пp]_?[еe]_?[рr]_?[еe]_?',
                '[oо]_?[дdg]_?[нnh]_?[оo]_?',
                '[кk]_?[oо]_?[нnh]_?[оo]_?',
                '[мm]_?[уy]_?[дdg]_?[oоaа]_?',
                '[oо]_?[сc]_?[тt]_?[оo]_?',
                '[дdg]_?[уy]_?[рpr]_?[оoаa]_?',
                '[хx]_?[уy]_?[дdg]_?[оoаa]_?',

                '[мm]_?[нnh]_?[оo]_?[гg]_?[оo]_?',
                '[мm]_?[оo]_?[рpr]_?[дdg]_?[оoаa]_?',
                '[мm]_?[оo]_?[зz3]_?[гg]_?[оoаa]_?',
                '[дdg]_?[оo]_?[лl]_?[бb6]_?[оoаa]_?',
                '[оo]_?[сc]_?[тt]_?[рpr]_?[оo]_?',
            ];

            $badwords = [
                '(?<=\PL) %RE_PRETEXT%?
                      [hхx]_?[уyu]_?[ийiеeёяюju]
                      #исключения:
                      (?<! _hue(?=_)     #HUE     -- цветовая палитра
                         | _hue(?=so_)   #hueso   -- испанское слово
                         | _хуе(?=дин)   #Хуедин  -- город в Румынии
                         | _hyu(?=ndai_) #Hyundai -- марка корейского автомобиля
                      )',

                '(?<=\PL) %RE_PRETEXT%?
                      [пp]_?[иieеё]_?[зz3]_?[дd](?=_?[:vowel:])',
                '(?<=\PL) %RE_PRETEXT%?
                      [eеё]_?
							#исключения
							(?<!н[eе][её]_|т_е_)    #неё, т.е. большие
                      [бb6]_? (?= [уyиi]_                       #ебу, еби
                                | [ыиiоoaаеeёуy]_?[:consonant:] #ебут, ебать, ебись, ебёт, поеботина, выебываться, ёбарь
                                   #исключения
                                  (?<!_ebo[kt](?=_)|буд)        #ebook, eboot, ее будут
                                | [лl](?:[оoаaыиiя]|ya)         #ебло, ебла, ебливая, еблись, еблысь, ёбля
                                | [нn]_?[уy]                    #ёбнул, ёбнутый
                                | [кk]_?[аa]                    #взъёбка
                                | [сc]_?[тt]                    #ебсти
                               )',

                '(?<=\PL) %RE_PRETEXT%
                      (?<= \pL\pL|\pL_\pL_)
                      [eеё]_?[бb6]    #долбоёб, дураёб, изъёб, заёб, заебай, разъебай, мудоёбы
            ',

                '(?<=\PL) ёб (?=\PL)',
                '(?<=\PL) %RE_PRETEXT%?
                      [бb6]_?[лl]_?(?:я|ya)(?: _         #бля
                                             | _?[тдtd]  #блять, бляди
                                           )',

                '(?<=\PL) [пp]_?[иieе]_?[дdg]_?[eеaаoо]_?[rpр]',

                '(?<=\PL) [мm]_?[уy]_?[дdg]_?[аa]  #мудак, мудачок
                      #исключения:
                      (?<!_myda(?=s_))  #Chelonia mydas -- морская зеленая (суповая) черепаха
            ',

                '(?<=\PL) [zж]_?h?_?[оo]_?[pп]_?[aаyуыiеeoо]',

                '(?<=\PL) [мm]_?[аa]_?[нnh]_?[дdg]_?[aаyуыiеeoо]  #манд[ауыео]
                      #исключения:
                      (?<! манда(?=[лн]|рин)
                         | manda(?=[ln]|rin)
                         | манде(?=ль)
                      )',
                '(?<=\PL) [гg]_?[оo]_?[вvb]_?[нnh]_?[оoаaяеeyу]',
                '(?<=\PL) f_?u_?[cс]_?k',
                ' л_?[оo]_?[хx]',
                '[^р]_?[scс]_?[yуu]_?[kк]_?[aаiи]',
                '[^р]_?[scс]_?[yуu]_?[4ч]_?[кk]',
                ' %RE_PRETEXT%?[хxh]_?[еe]_?[рpr](_?[нnh]_?(я|ya)| )',
                ' [зz3]_?[аa]_?[лl]_?[уy]_?[пp]_?[аa]',

            ];

            $trans = [
                '_'             => '\x20',
                '\pL'           => '[^\x20\d]',
                '\PL'           => '[\x20\d]',
                '[:vowel:]'     => '[аеиоуыэюяёaeioyu]',
                '[:consonant:]' => '[^аеиоуыэюяёaeioyu\x20\d]',
            ];

            $re_badwords = str_replace(
                '%RE_PRETEXT%',
                '(?:' . implode('|', $pretext) . ')',
                '~' . implode('|', $badwords) . '~sxuSX'
            );
            $re_badwords = strtr($re_badwords, $trans);
        }

        $s       = UTF8::convert_from($s, $charset);
        $replace = UTF8::convert_from($replace, $charset);

        $ss = $s;

        if ($is_html) {
            $s = is_callable(['HTML', 'strip_tags']) ? HTML::strip_tags($s, null, true, ['comment', 'style', 'map', 'frameset', 'object', 'applet'])
                : strip_tags($s);
            $s = UTF8::html_entity_decode($s, $is_htmlspecialchars = true);
        }

        if (strtoupper(substr($charset, 0, 3)) === 'UTF') {
            $additional_chars = [
                "\xc2\xad",
            ];
            $s = UTF8::diactrical_remove($s, $additional_chars);
        }

        if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
            $s = preg_replace('~     [\p{Lu}3] (?>\p{Ll}+|/\\\\|[@36]+)++   #Вот
								 (?= [\p{Lu}3] (?:\p{Ll} |/\\\\|[@36] ) )   #Бля
							   ~sxuSX', '$0 ', $s);
        }

        $s = UTF8::lowercase($s);

        preg_match_all('~(?> \xd0[\xb0-\xbf]|\xd1[\x80-\x8f\x91]  #[а-я]
						  |  /\\\\     #л
						  |  @         #а
						  |  [a-z\d]+
						  )+
						~sxSX', $s, $m);
        $s = ' ' . implode(' ', $m[0]) . ' ';

        $trans = [
            '/\\' => 'л',
            '@'   => 'а',
        ];
        $s = strtr($s, $trans);

        $trans = [
            '~ [3з]++ [3з\x20]*+ ~sxuSX' => 'з',
            '~ [6б]++ [6б\x20]*+ ~sxuSX' => 'б',
        ];
        $s = preg_replace(array_keys($trans), array_values($trans), $s);

        $s = preg_replace('/(  [\xd0\xd1][\x80-\xbf] \x20?  #optimized [а-я]
                             | [a-z\d] \x20?
                             ) \\1+
                           /sxSX', '$1', $s);

        if ($replace === null || version_compare(PHP_VERSION, '5.2.0', '<')) {
            $result = preg_match($re_badwords, $s, $m, PREG_OFFSET_CAPTURE);
            if (function_exists('preg_last_error') && preg_last_error() !== PREG_NO_ERROR) {
                return preg_last_error();
            }
            if ($result === false) {
                return 1;
            }  #PREG_INTERNAL_ERROR = 1
            if ($result && $replace === null) {
                list($word, $offset) = $m[0];
                $s1                  = substr($s, 0, $offset);
                $s2                  = substr($s, $offset + strlen($word));
                $delta               = intval($delta);
                if ($delta === 0) {
                    $fragment = '[' . trim($word) . ']';
                } else {
                    if ($delta < 1 || $delta > 10) {
                        $delta = 3;
                    }
                    preg_match('/  (?> \x20 (?>[\xd0\xd1][\x80-\xbf]|[a-z\d]+)++ ){1,' . $delta . '}+
                                   \x20?+
                                $/sxSX', $s1, $m1);
                    preg_match('/^ (?>[\xd0\xd1][\x80-\xbf]|[a-z\d]+)*+  #ending
                                   \x20?+
                                   (?> (?>[\xd0\xd1][\x80-\xbf]|[a-z\d]+)++ \x20 ){0,' . $delta . '}+
                                /sxSX', $s2, $m2);
                    $fragment = (ltrim($m1[0] ?? '') !== ltrim($s1) ? $continue : '') .
                        trim((isset($m1[0]) ? $m1[0] : '') . '[' . trim($word) . ']' . (isset($m2[0]) ? $m2[0] : '')) .
                        (rtrim($m2[0] ?? '') !== rtrim($s2) ? $continue : '');
                }

                return UTF8::convert_to($fragment, $charset);
            }

            return false;
        }

        $result = preg_match_all($re_badwords, $s, $m);
        if (function_exists('preg_last_error') && preg_last_error() !== PREG_NO_ERROR) {
            return preg_last_error();
        }
        if ($result === false) {
            return 1;
        }  #PREG_INTERNAL_ERROR = 1
        if ($result > 0) {
            #d($s, $m[0]);
            $s = $ss;
            #замена матного фрагмента на $replace
            foreach ($m[0] as $w) {
                $re_w = '~' . preg_replace_callback('~(?:/\\\\|[^\x20])~suSX', ['self', '_make_regexp_callback'], $w) . '~sxuiSX';
                $ss   = preg_replace($re_w, $replace, $ss);
                #d($re_w);
            }
            while ($ss !== $s) {
                $ss = self::parse($s = $ss, $delta, $continue, $is_html, $replace, 'UTF-8');
            }
        }

        return UTF8::convert_to($ss, $charset);
    }

    private static function _make_regexp_callback(array $m)
    {
        $re_holes = '(?!/\\\\)[^\p{L}\d]';
        if ($m[0] === 'а') {
            $re = '[@аА]++           (?>[:holes:]|[@аА]+)*+';
        } elseif ($m[0] === 'з') {
            $re = '[3зЗ]++           (?>[:holes:]|[3зЗ]+)*+';
        } elseif ($m[0] === 'б') {
            $re = '[6бБ]++           (?>[:holes:]|[6бБ]+)*+';
        } elseif ($m[0] === 'л') {
            $re = '(?>[лЛ]+|/\\\\)++ (?>[:holes:]|[лЛ]+|/\\\\)*+';
        } else {
            $char = '[' . preg_quote($m[0] . UTF8::uppercase($m[0]), '~') . ']';
            $re   = str_replace('$0', $char, '$0++ (?>[:holes:]|$0+)*+');
        }

        return str_replace('[:holes:]', $re_holes, $re . "\r\n");
    }
}
