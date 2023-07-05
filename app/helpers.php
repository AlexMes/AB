<?php

if (! function_exists('digits')) {
    function digits($string)
    {
        if (is_null($string)) {
            return null;
        }
        $result = trim(
            preg_replace('/[^0-9]/', '', $string)
        );

        return $result === '' ? null : $result;
    }
}

if (! function_exists('date_between')) {
    function date_between($date)
    {
        if (!empty($date) && !is_array($date)) {
            $date = ['since' => $date, 'until' => $date];
        }

        return $date;
    }
}

if (! function_exists('percentage')) {
    function percentage($one, $two)
    {
        if ($two == 0) {
            return 0;
        }

        if ($two !== null && $two !== 0) {
            return round(($one / $two) * 100, 2);
        }

        return 0;
    }
}

if (! function_exists('zero_safe_division')) {
    function zero_safe_division($dividend, $divisor)
    {
        if ($divisor !== null && $divisor !== 0) {
            try {
                return $dividend / $divisor;
            } catch (Throwable $exception) {
                return 0;
            }
        }

        return 0;
    }
}

if (! function_exists('msr')) {
    function msr($label, callable $callback)
    {
        $start  = microtime(true);
        $result = call_user_func($callback);
        Log::warning($label.' '.(microtime(true) - $start));

        return $result;
    }
}

if (! function_exists('trail')) {
    function trail($message)
    {
        app(App\Trail::class)->add($message);
    }
}


if (! function_exists('nullstr')) {
    function nullstr($string)
    {
        if (is_null($string)) {
            return null;
        }

        return trim($string) === '' ? null : $string;
    }
}
