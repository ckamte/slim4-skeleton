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
                // clean up empty lines
                $buffer = str_replace(["\r\n", "\n", "\r"], '', $buffer);

                if ((strlen($buffer) > 0) && (str_contains($buffer, '='))) {
                    // create env array from first equal sign only
                    $tmp = explode('=', $buffer, 2);
                    // remove white spaces and quotes
                    $key = trim($tmp[0]);
                    $val = trim(str_replace('"', '', $tmp[1]));
                    $defaults[$key] = $val;
                }
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
