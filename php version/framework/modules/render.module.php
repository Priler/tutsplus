<?php

/**
 * Render module.
 * Providers template rendering functionality.
 * Twig templating system is integrated.
 */

function render($template, $variables = array(), $force_display = true) {

  $path_parts = explode('/', $template);
  foreach( $path_parts as &$pp )
  {
    $pp = ucfirst(strtolower($pp));
  }
  $path_parts[] = strtolower(array_pop($path_parts));

  $loader = new Twig_Loader_Filesystem( TEMPLATE_DIR );
  if( count($et = explode('/', $template)) > 1 )
  {
    if( file_exists(TEMPLATE_DIR . '/' . join('/', array_slice($path_parts, 0, count($path_parts) - 1))) )
    {

      for( $i = 0; $i < count($et) - 1; $i++)
      {
        $loader->addPath( TEMPLATE_DIR . '/' . ucfirst(strtolower(array_shift($et))) );
      }
    }
    $template = array_pop($et);
  }

  $loader->addPath( TEMPLATE_DIR . '/' . ACTIVE_APP );

  $twig_render_params = array();
  $app_settings = App::settings();
  if( $app_settings['twig']['cache_enabled'] )
  {
    $twig_render_params['cache'] = TWIG_CACHE_DIR;
  }

  $twig = new Twig_Environment($loader, $twig_render_params);
  $variables['app']['settings'] = $app_settings;
  $variables['app']['view'] = ACTIVE_VIEW;
  $variables['app']['self_url'] = SELF_URL;
  $variables['app']['host'] = get_protocol() . $_SERVER['HTTP_HOST'];
  $variables['apps'] = twig_getvars();

  if( $force_display )
  {
    $twig->display(
      $template . '.html',
      $variables
    );
  } else
  {
    return $twig->render(
      $template . '.html',
      $variables
    );
  }

}

function render_url($template, $variables = array()) {

  return array( 'render_url' => array($template, $variables) );

}

function redirect($url) {
  header('Location:' . $url);
  exit();
  return true;
}

function drop_404() {
  render('404');
  exit();
}