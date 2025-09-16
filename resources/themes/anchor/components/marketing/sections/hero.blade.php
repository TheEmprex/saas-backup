<section class="relative top-0 flex flex-col items-center justify-center w-full min-h-screen -mt-24 bg-white lg:min-h-screen">
    
        <div class="flex flex-col items-center justify-between flex-1 w-full max-w-2xl gap-6 px-8 pt-32 mx-auto text-left md:px-12 xl:px-20 lg:pt-32 lg:pb-16 lg:max-w-7xl lg:flex-row">
            <div class="w-full lg:w-1/2">
                <h1 class="text-6xl font-bold tracking-tighter text-left sm:text-7xl md:text-8xl sm:text-center lg:text-left text-zinc-900 text-balance">
                    <span class="block origin-left lg:scale-90 text-nowrap">Only Verified</span> <span class="pr-4 text-transparent text-neutral-600 bg-clip-text bg-gradient-to-b from-neutral-900 to-neutral-500">Talent</span>
                </h1>
                <p class="mx-auto mt-5 text-2xl font-normal text-left sm:max-w-md lg:ml-0 lg:max-w-md sm:text-center lg:text-left text-zinc-500">
                    Connect with verified OnlyFans chatters, VAs, and agencies<span class="hidden sm:inline"> on the premium marketplace</span>.
                </p>
                <div class="flex flex-col items-center justify-center gap-3 mx-auto mt-8 md:gap-2 lg:justify-start md:ml-0 md:flex-row">
                    <x-button href="{{ route('custom.register') }}" tag="a" size="lg" class="w-full lg:w-auto">Join OnlyVerified</x-button>
                    <x-button href="{{ route('marketplace.index') }}" tag="a" size="lg" color="secondary" class="w-full lg:w-auto">Browse Talent</x-button>
                </div>
            </div>
            <div class="flex items-center justify-center w-full mt-12 lg:w-1/2 lg:mt-0">
                <img alt="Wave Character" class="relative w-full lg:scale-125 xl:translate-x-6" src="/wave/img/character.png" style="max-width:450px;">
            </div>
        </div>
        <div class="flex-shrink-0 lg:h-[150px] flex border-t border-zinc-200 items-center w-full bg-white">
            <div class="grid h-auto grid-cols-1 px-8 py-10 mx-auto space-y-5 divide-y max-w-7xl lg:space-y-0 lg:divide-y-0 divide-zinc-200 lg:py-0 lg:divide-x md:px-12 lg:px-20 lg:divide-zinc-200 lg:grid-cols-3">
                <div class="">
                    <h3 class="flex items-center font-medium text-zinc-900">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Verified Professionals
                    </h3>
                    <p class="mt-2 text-sm font-medium text-zinc-500">
                        All talent goes through strict verification process. <span class="hidden lg:inline">Only qualified professionals join our platform.</span>
                    </p>
                </div>
                <div class="pt-5 lg:pt-0 lg:px-10">
                    <h3 class="flex items-center font-medium text-zinc-900">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Secure Marketplace
                    </h3>
                    <p class="mt-2 text-sm text-zinc-500">
                        Safe, encrypted platform with escrow protection. <span class="hidden lg:inline">Your transactions are always secure.</span>
                    </p>
                </div>
                <div class="pt-5 lg:pt-0 lg:px-10">
                    <h3 class="flex items-center font-medium text-zinc-900">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                        Premium Support
                    </h3>
                    <p class="mt-2 text-sm text-zinc-500">
                        24/7 dedicated support team for all your needs and concerns.
                    </p>
                </div>
            </div>
        </div>
</section>