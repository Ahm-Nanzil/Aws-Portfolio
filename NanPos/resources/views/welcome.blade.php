@extends( 'layout.base' )

@section( 'layout.base.body' )
    <div id="page-container" class="h-full w-full flex">
        <div class="container flex-auto flex-col items-center justify-center flex m-4 sm:mx-auto">
            <div class="flex justify-center items-center py-6">
                @if ( ! ns()->option->get( 'ns_store_square_logo', false ) )
                <img class="w-32" src="{{ asset( 'svg/possoft-variant-1.svg' ) }}" alt="PosSoft">
                @else
                <img class="w-[200px]" src="{{ ns()->option->get( 'ns_store_square_logo' ) }}" alt="PosSoft">
                @endif
            </div>
            <div class="ns-box rounded shadow w-full md:w-2/5 lg:w-2/5 overflow-hidden">
                <div id="section-header" class="ns-box-header p-4">
                    <p class="text-center b-8 text-sm">{{ __( "If you see this page, this means PosSoft is correctly installed on your system. This page shows useful links that will take you to important resources." ) }}</p>
                </div>
                <div class="ns-box-footer flex shadow border-t">
                    <div class="flex w-1/3"><a class="link text-sm w-full py-2 text-center" href="{{ ns()->route( 'ns.dashboard.home' ) }}">{{ __( 'Dashboard' ) }}</a></div>
                    <div class="flex w-1/3"><a class="link text-sm w-full py-2 text-center" href="{{ ns()->route( 'ns.login' ) }}">{{ __( 'Sign In' ) }}</a></div>
                    <div class="flex w-1/3"><a class="link text-sm w-full py-2 text-center" href="{{ ns()->route( 'ns.register' ) }}">{{ __( 'Sign Up' ) }}</a></div>
                </div>
            </div>
        </div>
    </div>
@endsection
