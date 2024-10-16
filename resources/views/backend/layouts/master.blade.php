<!DOCTYPE html>
 
<html lang="en" class="light">
    <!-- BEGIN: Head -->
    <head>
     
      @include("backend.layouts.head")
      @yield('css')
      @yield('topscript')
    </head>
    <!-- END: Head -->
    <body class="py-5 md:py-0">
        @include("backend.layouts.menumobile")
        @include("backend.layouts.topbar")
        <div class="flex overflow-hidden">
            <!-- BEGIN: Side Menu -->
            @include("backend.layouts.sidemenu")
            <!-- END: Side Menu -->
            <!-- BEGIN: Content -->
            <div class="content">
                @yield('content')
            </div>
            <!-- END: Content -->
        </div>
        
        
        <!-- BEGIN: JS Assets-->
        @yield('botscript')
        <script src="{{asset('backend/js/app.js')}}"></script>
        <!-- END: JS Assets-->
    </body>
</html>