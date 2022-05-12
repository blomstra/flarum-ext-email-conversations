<?php

namespace Blomstra\PostByMail;

use Exception;

// Direct copy from https://github.com/FriendsOfFlarum/pretty-mail/blob/1.0.1/src/BladeCompiler.php
class BladeCompiler
{
    public static function render($string, $data)
    {
        $php = resolve('blade.compiler')->compileString($string);

        $obLevel = ob_get_level();
        ob_start();
        extract($data, EXTR_SKIP);

        try {
            eval('?'.'>'.$php);
        } catch (\Exception $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            throw $e;
        } catch (\Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            throw new Exception($e);
        }

        return ob_get_clean();
    }
}
