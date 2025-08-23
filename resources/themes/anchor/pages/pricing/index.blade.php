<?php
    use function Laravel\Folio\{middleware, name};
    name('pricing');
?>

<x-layouts.marketing
    :seo="[
        'title' => 'Pricing Plans - OnlyVerified',
        'description' => 'Choose the perfect plan for your OnlyFans ecosystem business. Transparent pricing for chatters and agencies.',
    ]"
>

    <x-container class="py-10">
        <x-marketing.sections.pricing />
    </x-container>

</x-layouts.marketing>
