<?php declare(strict_types=1);

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
        'defaults' => function () {
            /* Read default config data from env file */
            // env file location
            $env_file = __DIR__ . '/../../.env';

            // check and open env file
            if (!$handle = @fopen($env_file, 'r')) {
                die('ENV file not found');
            }
            $defaults = [];

            // read env file
            while (($buffer = fgets($handle, 4096)) !== false) {
                // clean up unnecessary white spaces
                $buffer = trim($buffer);

                // ignoring comments and empty lines
                if ((str_starts_with($buffer, '#')) || (!str_contains($buffer, '='))) {
                    continue;
                }

                // create env array from first equal sign only
                $tmp = explode('=', $buffer, 2);

                // remove white spaces and quotes
                $key = trim($tmp[0]);

                // for line comment
                $val = trim($tmp[1]);
                if (str_contains($val, '"')) {
                    preg_match('~"(.*?)"~', $val, $param);
                    $param = $param[1];
                } elseif (str_contains($val, '\'')) {
                    preg_match('~\'(.*?)\'~', $val, $param);
                    $param = $param[1];
                } else {
                    $param = strtok($val, '#');
                }

                $defaults[$key] = trim($param);
            }
            // close file
            fclose($handle);

            /* Get base path */
            $basePath = '';
            if (isset($_SERVER['REQUEST_URI'])) {
                // Current url path
                $curUrl = (string)parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                
                // Current script name
                $scriptName = $_SERVER['SCRIPT_NAME'];
                
                // Remove last file path (Assumed using public folder)
                $scriptName = str_replace('\\', '/', dirname($scriptName, 2));
                
                // Get path for slim
                if ($scriptName != '/') {
                    $length = strlen($scriptName);
                    if ($length > 0) {
                        $basePath = substr($curUrl, 0, $length);
                    }
                }
            }
            // Remove tailing back-slash
            $defaults['BASE_PATH'] =  rtrim($basePath, '/');

            return $defaults;
        }
        ]
    );
};
