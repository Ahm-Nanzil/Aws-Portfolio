<?php

namespace App\Widgets;

use App\Services\WidgetService;

class MyPosSoftWidget extends WidgetService
{
    protected $vueComponent = 'nsMyPosSoftWidget';

    protected string $layout = '1x3';

    protected string $layoutPolicy = 'restricted';

    protected array $supportedLayouts = [ '1x3', '1x2', '2x3' ];

    public function __construct()
    {
        $this->name = __( 'My PosSOFT' );
        $this->description = __( 'Invite users to connect this installation to My PosSOFT and unlock marketplace extensions.' );
        $this->permission = 'manage.modules';
    }

    public function getData(): array
    {
        return [
            'isConnected' => ! empty( ns()->option->get( 'mypossoft_access_token' ) ) && ! empty( ns()->option->get( 'mypossoft_refresh_token' ) ),
        ];
    }
}
