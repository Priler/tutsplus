<?php

class PrilerEngine {

    public
        $settings, // settings
        $uri, // current URI
        $app; // current app

    public function __construct($settings) {
        $this->settings = $settings;
        $this->uri = urldecode(preg_replace('/\?.*/iu', '', $_SERVER['REQUEST_URI']));
        $this->app = false;
        $this->process_path();
        $this->process_twigs();
        $this->process_controllers();
    }

    public function process_path() {
        foreach( $this->settings['apps'] as $iterable_app )
        {
            $urls_file = BASE_DIR . '/apps/' . $iterable_app . '/urls.php';
            if( !file_exists($urls_file) )
            {
                continue;
            }
            $iterable_urls = require($urls_file);
            foreach( $iterable_urls as $pattern => $method )
            {
                $matches = array();
                if( preg_match($pattern, $this->uri, $matches) )
                {
                    define('ACTIVE_APP', $iterable_app);
                    $this->app = array($iterable_app, array('pattern' => $pattern, 'method' => $method, 'args' => $matches));
                    break(2);
                }
            }
        }

        if( $this->app === false )
        {
            exit('App not found.');
        }
    }

    public function process_twigs() {
        foreach( $this->settings['apps'] as $iterable_app )
        {
            if( file_exists(BASE_DIR . '/apps/' . $iterable_app . '/twig.php') )
            {
                if( file_exists(BASE_DIR . '/apps/' . $iterable_app . '/functions.php') )
                {
                    require BASE_DIR . '/apps/' . $iterable_app . '/functions.php';
                }

                twig_setvars( array_merge(twig_getvars(), array(
                strtolower($iterable_app) => require BASE_DIR . '/apps/' . $iterable_app . '/twig.php')) );
            }
        }
    }

    public function process_controllers() {
        if( $this->app || is_array($this->app) )
        {
            if( isset($this->app['1']['method']) && isset($this->app['1']['method']['render_url']) )
            {
                // direct rendering
                require(BASE_DIR . '/apps/' . $this->app['0'] . '/controller.php');
                $controller_name = $this->app['0'] . '_Controller';
                $this->app_controller = new $controller_name();
                $this->app_controller->app = $this;
                render($this->app['1']['method']['render_url']['0'], $this->app['1']['method']['render_url']['1']);
            } else
            {
                // method rendering
                require(BASE_DIR . '/apps/' . $this->app['0'] . '/controller.php');
                $controller_name = $this->app['0'] . '_Controller';
                $this->app_controller = new $controller_name();
                $this->app_controller->app = $this;
                define('SELF_URL', 'http://'.$_SERVER['HTTP_HOST'] . $this->uri);
                define('ACTIVE_VIEW', $this->app['1']['method']);

                $this->app_controller->{$this->app['1']['method']}($this->app['1']['args']);
            }
        }
    }

}