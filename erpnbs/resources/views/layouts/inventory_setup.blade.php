<div class="card sticky-top" style="top:30px">
    <div class="list-group list-group-flush" id="useradd-sidenav">
        <a href="{{ route('inventory-general.index') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'inventory-general.index' ) ? ' active' : '' }}">{{__('General')}}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        <a href="{{ route('inventory.product-types') }}" class="list-group-item list-group-item-action border-0">{{__('Product Types')}}
          <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        <a href="{{ route('inventory.product-categories') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'inventory.product-categories' ) ? ' active' : '' }}">{{__('Product Categories')}}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        <a href="{{ route('product-unit.index') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'product-unit.index' ) ? ' active' : '' }}">{{__('Unit')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

        <a href="{{ route('inventory.colors') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'inventory.colors' ) ? ' active' : '' }}">{{__('Colors')}}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        <a href="{{ route('inventory.model') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'inventory.model' ) ? ' active' : '' }}">{{__('Models')}}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        <a href="{{ route('inventory.style') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'inventory.style' ) ? ' active' : '' }}">{{__('Styles')}}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        <a href="{{ route('inventory.custom') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'inventory.custom' ) ? ' active' : '' }}">{{__('Warehouse Custom Fields')}}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        <a href="{{ route('inventory.minmax') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'inventory.minmax' ) ? ' active' : '' }}">{{__('Minimum, Maximum Inventory')}}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        <a href="{{ route('inventory.prefix') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'inventory.prefix' ) ? ' active' : '' }}">{{__('Prefix Settings')}}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        <a href="{{ route('inventory.approval') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'inventory.approval' ) ? ' active' : '' }}">{{__('Approval Settings')}}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        <a href="{{ route('inventory.permission') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'inventory.permission' ) ? ' active' : '' }}">{{__('Permissions')}}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        <a href="{{ route('inventory.reset') }}" class="list-group-item list-group-item-action border-0 {{ (Request::route()->getName() == 'inventory.reset' ) ? ' active' : '' }}">{{__('Reset Data')}}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
    </div>

</div>
