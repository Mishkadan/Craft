<?php
return [
    '@class' => 'Gantry\\Component\\File\\CompiledYamlFile',
    'filename' => 'C:\\OpenServer\\domains\\cr.loc/templates/g5_helium/custom/config/12/page/head.yaml',
    'modified' => 1659363800,
    'data' => [
        'head_bottom' => ' <script src="https://api-maps.yandex.ru/2.1/?apikey=a4691c36-e9ad-4a81-96ed-a185938751a2&lang=ru_RU" type="text/javascript ">  </script>',
        'atoms' => [
            0 => [
                'id' => 'assets-1718',
                'type' => 'assets',
                'title' => 'Custom CSS / JS',
                'attributes' => [
                    'enabled' => '1',
                    'css' => [
                        0 => [
                            'location' => 'gantry-assets://custom/scss/header-js.css',
                            'inline' => '',
                            'extra' => [
                                
                            ],
                            'priority' => '0',
                            'name' => 'header-js'
                        ]
                    ],
                    'javascript' => [
                        0 => [
                            'location' => '',
                            'inline' => 'jQuery( document ).ready(function() {
   jQuery(\'#nav-icon3\').click(function(){
       jQuery(this).toggleClass(\'open\');
    });
});',
                            'in_footer' => '0',
                            'extra' => [
                                
                            ],
                            'priority' => '0',
                            'name' => 'бургер меню'
                        ],
                        1 => [
                            'location' => 'gantry-assets://js/custom.js',
                            'inline' => '',
                            'in_footer' => '0',
                            'extra' => [
                                
                            ],
                            'priority' => '0',
                            'name' => 'custom'
                        ]
                    ]
                ]
            ]
        ]
    ]
];
