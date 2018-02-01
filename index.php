    <?php

    require 'vendor/autoload.php';


    $arr = [
        '1stLevelKey' => '1stLevelValue0',
        0 => [
            '2ndLevelKey' => [
                '3rdLevelKey' => '3rdLevelValue0',
                0             => '3rdLevelValue1',
            ]
        ],
        '1stLevelValue1'
    ];

    $obj = new \Arr\Arr($arr);

    foreach($obj->walkRecursive() as $key => $value) {
        var_dump($key . ':' . $value);
    }

    var_dump($obj->nth(2));