<?php
require_once __DIR__ . '/Utils/ArrayWalker/ArrayWalker.php';
require_once __DIR__ . '/Utils/ArrayWalker/ArrayWalkerTrait.php';
require_once __DIR__ . '/Utils/Timer/Timer.php';
require_once __DIR__ . '/Utils/Timer/TimedItem.php';
require_once __DIR__ . '/Utils/WebLoader/WebLoader.php';
require_once __DIR__ . '/Utils/WebLoader/WebLoaderItem.php';
require_once __DIR__ . '/Utils/WebLoader/WLCreator.php';
require_once __DIR__ . '/Utils/Subjects/Subjects.php';
require_once __DIR__ . '/Utils/Subjects/Subject.php';
require_once __DIR__ . '/Utils/Subjects/Helpers.php';

// install vendor in this package for testing
require_once __DIR__ . '/../vendor/autoload.php';

class ExampleDataStorage
{
    use \BiteIT\Utils\ArrayWalkerTrait;

    protected $data = [
        'level-1' => [
            'value' => true,
            'level-2' => [
                'value' => true,
                'level-3' => [
                    'value' => true
                ]
            ]
        ]
    ];

    public function getDataContainerName()
    {
        return 'data';
    }
}