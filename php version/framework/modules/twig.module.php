<?php

function twig_getvars() {
  return $GLOBALS['render_twig_vars'];
}

function twig_setvars($vars) {
  $GLOBALS['render_twig_vars'] = $vars;
}

twig_setvars(array());