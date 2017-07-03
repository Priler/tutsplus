<?php

if( ! user() ) {
  return array(
  'authorized' => false);
} else
{
  return array(
    'authorized' => true,
    'active' => user());
}