<?php


$cmd    = $argv[0];
$name   = $argv[1];

if (!$name) {
    die("Write a name for your application. The name will be the PHP class name of your application base class. a-z, 0-9 and _ are allowed\n");
}

$nice   = strtolower($name);

if (!preg_match('|^[a-z][\w\d_]*$|', $nice)) {
    die("Not a valid name, only a-z0-9 are allowed: $name ($nice)\n");
}
if (is_dir($nice)) {
    die("A directory exists by that name: $nice\n");
}
if (file_exists($nice)) {
    die("A file exists by that name: $nice\n");
}

$path   = preg_replace('|_|', "/", $name);

mkdir($nice);
mkdir("$nice/lib");
mkdir("$nice/lib/$path", 0777, true);

$context = <<<EOF
<?php
require_once 'DF/Web.php';
class $name extends DF_Web {
}
EOF;
file_put_contents("$nice/lib/$path.php", $context);



$config = <<<EOF
---

Controller_Root:
    namespace:  ""
    actions:
        chained:
            # Chained: /
            chained:    /
            path:       ""
        index:
            # Matches: /
            chained:    chained
            path:       ""
            args:       0
        default:
            # Matches: /*
            chained:    chained
            path:       ""
            args:
EOF;
file_put_contents("$nice/config.yaml", $config);

mkdir("$nice/lib/$path/Controller");
mkdir("$nice/lib/$path/Model");
mkdir("$nice/lib/$path/View");

$root_ctrl = <<<EOF
<?php

class ${name}_Controller_Root extends DF_Web_Controller {
    public static \$LOGGER = NULL;
    
    
    # Run once for this controller per request
    public function handle_auto(\$c) {
        error_log("This is Root/auto");
        return true;
    }
    
    # Chained to the root, see config.yaml
    public function handle_chained(\$c) {
        error_log("This is Root/chained");
    }
    
    # The index page, see config.yaml
    public function handle_index(\$c) {
        error_log("This is Root/index");
        \$c->response->content_type('text/plain');
        \$c->response->body("Hello world");
        return true;
    }
    
    # The default page, see config.yaml
    # Fallbacks to this if on other actions match
    public function handle_default(\$c) {
        error_log("This is Root/default");
        \$c->response->status(404);
        \$c->response->content_type('text/plain');
        \$c->response->body("Nothing found");
        return true;
    }
}
EOF;
file_put_contents("$nice/lib/$path/Controller/Root.php", $root_ctrl);


mkdir("$nice/www");

$dot_htaccess = <<<EOF
SetEnv ENVIRONMENT 'development'

RewriteEngine On
RewriteBase /$nice
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /$nice/index.php
EOF;
file_put_contents("$nice/www/.htaccess", $dot_htaccess);


$index = <<<EOF
<?php
# index.php
\$DIR_SCRIPT = dirname(__FILE__);
\$DIR_ROOT   = "\$DIR_SCRIPT/..";
\$DIR_LIB    = "\$DIR_ROOT/lib";

set_include_path(\$DIR_LIB.':'.get_include_path());

require_once 'DF/Web/Logger.php';
DF_Web_Logger::setActiveLogger('error_log');

require_once '$path.php';

\$environment = DF_Web_Environment::singleton();
\$environment->app_root = \$DIR_ROOT;
\$environment->trusted_proxies = 0;
\$environment->base_path = dirname(\$_SERVER['SCRIPT_NAME']);

if (getenv('ENVIRONMENT')) {
    \$environment->environment = getenv('ENVIRONMENT');
}

\$environment->debug = 1;

# for production
#\$environment->environment = 'production';
#\$environment->debug = 0;

\$context = new $name();
\$context->execute();
\$context->finalize();
\$context = NULL;

DF_Web_Logger::shutdown();
EOF;
file_put_contents("$nice/www/index.php", $index);
