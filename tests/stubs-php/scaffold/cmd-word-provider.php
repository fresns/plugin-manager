<?php

namespace $NAMESPACE$;

use Illuminate\Support\ServiceProvider;

class $CLASS$ extends ServiceProvider implements \Fresns\CmdWordManager\Contracts\CmdWordProviderContract
{
    use \Fresns\CmdWordManager\Traits\CmdWordProviderTrait;

    protected $unikeyName = '$STUDLY_NAME$';

    /**
     *
     * @example

    use Plugins\BarBaz\Models\TestModel;
    use Plugins\BarBaz\Services\AWordService;
    use Plugins\BarBaz\Services\BWordService;

    protected $cmdWordsMap = [
        ['word' => AWordService::CMD_TEST, 'provider' => [AWordService::class, 'handleTest']],
        ['word' => BWordService::CMD_STATIC_TEST, 'provider' => [BWordService::class, 'handleStaticTest']],
        ['word' => TestModel::CMD_MODEL_TEST, 'provider' => [TestModel::class, 'handleModelTest']],
    ];

     * @var array[]
     */
    protected $cmdWordsMap = [
        //
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCmdWordProvider();
    }
}
