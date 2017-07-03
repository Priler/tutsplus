<?php

function get_select_options($id)
{
  $select = R::load('selects', $id);
  return join(',', explode("\n", $select->options));
}