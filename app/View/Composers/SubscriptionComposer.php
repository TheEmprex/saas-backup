<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\View\View;
use App\Services\SubscriptionLayoutService;

class SubscriptionComposer
{
    protected $subscriptionLayoutService;
    
    public function __construct(SubscriptionLayoutService $subscriptionLayoutService)
    {
        $this->subscriptionLayoutService = $subscriptionLayoutService;
    }
    
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $subscriptionContext = $this->subscriptionLayoutService->getSubscriptionContext();
        
        $view->with([
            'subscriptionContext' => $subscriptionContext,
            'userSubscription' => $subscriptionContext['subscription'],
            'userFeatures' => $subscriptionContext['features'],
            'userLimits' => $subscriptionContext['limits'],
            'uiConfig' => $subscriptionContext['ui'],
        ]);
    }
}
