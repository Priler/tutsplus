<?php

return array(
    '#^/admin/*$#i' => render_url('main'),

    '#^/admin/users/*$#i' => 'users',
    '#^/admin/users/(learners)/*$#i' => 'users',
    '#^/admin/users/(mentors)/*$#i' => 'users',

    '#^/admin/articles/list/*$#i' => render_url('soon'),
    '#^/admin/articles/categories/*$#i' => render_url('soon'),

    '#^/admin/courses/list/*$#i' => render_url('soon'),
    '#^/admin/courses/categories/*$#i' => render_url('soon'),

    '#^/admin/other/pages/*$#i' => 'pages',

    '#^/admin/.*$#i' => render_url('soon'),
);