/*
 * Sensor data portal
 * Author: Pim van Gennip (pim@iconize.nl)
 *
 */

var app = angular.module('app', ['ngTouch', 'ng-iscroll', 'ngRoute', 'angular-gestures', 'ngSanitize', 'angularMoment', 'chart.js', 'smoothScroll', 'ngDialog', 'iconFilters', 'textFilters', 'uiSwitch', 'revolunet.stepper']);


/* Run some basic functions */
app.run(function($rootScope, $location, $window, $route, amMoment, ngDialog, smoothScroll, settings, api) 
{

    // set fastclick
    FastClick.attach(document.body);   

    // set the locale of moment.ks
    amMoment.changeLocale('en');

    // set the chart colors 
    Chart.defaults.global.defaultFontFamily = "'DinPro', 'MAIN', sans-serif";
    Chart.defaults.global.defaultFontSize   = 12;
    Chart.defaults.global.defaultFontStyle  = "normal";
    Chart.defaults.global.defaultFontColor  = "#444444";
    Chart.defaults.global.animation.easing  = "easeInOutCubic";
    Chart.defaults.global.animation.duration= "1000";
    Chart.defaults.global.tooltips.enabled  = false;
    Chart.defaults.global.tooltips.mode     = "single";
    Chart.defaults.global.responsive        = false;
    Chart.defaults.global.elements.line.borderWidth = 1;
    Chart.defaults.global.elements.line.borderColor = "#000000";
    Chart.defaults.global.elements.point.radius = 0;
    Chart.defaults.global.elements.point.borderColor = "#444444";
    Chart.defaults.global.elements.point.borderWidth = 1;
    Chart.defaults.global.elements.rectangle.borderWidth = 0;
    Chart.defaults.global.elements.rectangle.borderColor = "#444444";
    //Chart.defaults.global.elements.arc.borderColor = "#D9D9D9";

    $rootScope.device                = 'ios';

    // loading 
    $rootScope.loading               = true;
    $rootScope.controller_id         = null;
    $rootScope.status                = '';

    // set some root variables
    $rootScope.showMainMenu          = false;
    $rootScope.showBack              = false;
    $rootScope.showHeaderDetails     = false;
    $rootScope.showSplash            = false; //true;

    $rootScope.keyboardIsOpen        = false;

    $rootScope.pageSlug              = '';
    $rootScope.templateClass         = '';
    $rootScope.showAdminTemplate     = false;

    // set the language
    $rootScope.lang                  = LANG['en'];

    $rootScope.user                  = {name:''};

    
    //go to page
    $rootScope.goToPage = function(page)
    {
        $location.path(page);
    };

    //device check
    $rootScope.setDevice = function()
    {
    	if(runsNative())
    	{
    		$rootScope.device = (device.platform.toLowerCase() == 'ios') ? 'ios' : 'android';
            if($rootScope.device == 'android')
            {
                //document.getElementsByTagName('body')[0].className+='android';
            }
	     
	        // check for tablet
	        if(window.isTablet)
	        {
	        	$rootScope.mobile     = false;
	        	$rootScope.screenType = 'landscape';
	        	window.screen.lockOrientation('landscape');
	        }
	        else
	        {
	        	$rootScope.mobile     = true;
	        	$rootScope.screenType = 'mobile';
	        	window.screen.lockOrientation('portrait');
	        }
    	}
    	else
    	{
    		// browser code
    		var width = window.innerWidth;
	        $rootScope.mobile     = false;
	        $rootScope.screenType = 'ipad';
	        if(width < 768)
	        {
	            $rootScope.mobile = true; 
	            $rootScope.screenType = 'mobile';
	        }
    	}

    	$rootScope.$broadcast('screenSizeChange');
	    $rootScope.$digest();
    }
    $rootScope.setDevice();

    // add the resize listener
    $window.addEventListener('resize', $rootScope.setDevice);


    //@tmp
    //api.reset();


    // check if we have an api token
    $rootScope.checkToken = function()
    {
        // check the token
        if(api.getApiToken() == null)
        {
            // redirect to login
            console.log('$rootScope.checkToken: no token -> login');
            $location.path('/login');
        }
        else
        {
            // fetch the settings
            console.log('$rootScope.checkToken: token available -> fetchSettings');
            //$location.path('/load');
            settings.fetchSettings();
        }
    };


    setTimeout(function()
    {  
        $rootScope.$apply(function()
        {
            $rootScope.loading = false;
            $rootScope.checkToken();
        });
    }, 200);



    // check if we want header details
    $rootScope.$on('$routeChangeSuccess', function() 
    {
        // reset the vars
        $rootScope.showBack          = false;
        $rootScope.showHeaderDetails = true;

        // get the path
        var p           = $location.path();
        var slug        = p.split('/')[1];

        $rootScope.pageSlug = slug;
        $rootScope.defineTemplateClass(slug);

        // hide the details
        if(slug == 'login' || slug == 'settings')
        {
            // show the backbutton
            if(p == '/login/create')
            {
                $rootScope.showBack = true;
            }
            $rootScope.showHeaderDetails = false;
        }
    });

    $rootScope.defineTemplateClass = function(slug)
    {
        var className = '';
        var showAdmin = false;

        if ($rootScope.showSplash)
        {
            className = 'splash';
        }
        else
        {
            switch(slug)
            {
                case 'login':
                    className = 'login-page';
                    break;
                case 'create':
                    className = 'register-page';
                    break;
                case 'reminder':
                case 'reset':
                case 'logout':
                    className = 'login-page';
                    break;
                default:
                    showAdmin = true;
            }
        }

        $rootScope.showAdminTemplate = showAdmin;
        $rootScope.templateClass     = className;
    }


    // switch to a menu item
    $rootScope.switchMenu = function(e, doLink, link)
    {
        // check if we want to link
        doLink = typeof doLink !== 'undefined' ? doLink : false;

        e.preventDefault();
        if(doLink)
        {
            $location.path(link);
        }

        // switch the class
        $rootScope.showMainMenu = ($rootScope.showMainMenu == false) ? true : false;
    };


    //close menu overlay
    $rootScope.closeMenu = function()
    {
        // switch the class
        $rootScope.showMainMenu = ($rootScope.showMainMenu == false) ? true : false;
    };    



    $rootScope.scrollToView = function(view)
    {
        setTimeout(function()
        {
            $rootScope.$apply(function()
            {
                var element = document.querySelector('#view-'+view);
                var options = 
                {
                    duration    : 100,
                    easing      : 'easeOutCubic',
                    offset      : 0,
                    containerId : 'view-container',
                    direction   : 'horizontal',
                }
                smoothScroll(element, options);
            });
        }, 0);
    };


    $rootScope.loginStatus = '';



    // basic history function 
    var history = [];

    $rootScope.$on('$routeChangeSuccess', function() 
    {
        history.push($location.$$path);
    });


    $rootScope.back = function(e)
    {
		if($route.current.scope.back != null)
            $route.current.scope.back();
    };



    // handle the native backbutton
    $rootScope.handleBackButton = function()
    {
    	document.addEventListener("backbutton", function(e)
    	{
    		// prevent default
    		e.preventDefault();

    		// apply
    		$rootScope.$apply(function()
            {
            	$rootScope.$broadcast('backbutton');
            });
    	});
    };

    $rootScope.handleBackButton();


    //***************/
    /*   MESSAGES   */
    /***************/
    $rootScope.showMessage = function(message, callback, title, buttonName) 
    {
        title      = title || "";
        buttonName = buttonName || 'OK';

        if(navigator.notification && navigator.notification.alert) 
        {
            navigator.notification.alert(
                message,    // message
                callback,   // callback
                title,      // title
                buttonName  // buttonName
            );
        } 
        else 
        {
            nalert(message);
            if(callback != null)
            {
                callback();
            }
        }
    };



   	/***************/
    /*    FORMS    */
    /***************/
    $rootScope.validateFields = function(inputs, form, fields)
	{
		var valid = true;
		var error = null;

		for(var i in inputs)
		{
			if(!form[i].$valid)
			{
				var required = !!form[i].$error.required;
				var email    = !!form[i].$error.email;
				var password = !!form[i].$error.passwordMatch;

				var msg = '';
				if(required)
				{
					msg = $rootScope.lang.empty_fields;
				}
				else if(email)
				{
					msg = $rootScope.lang.no_valid_email;
				}
				else if(password)
				{
					msg = $rootScope.lang.match_passwords;
				}

				fields[i] = true;
				error = 
				{
					show          : true,
					resultType    : 'error',
					resultMessage : msg,
				};

				valid = false;
			}
		}

		// check if its valid
		if(!valid)
			return error;

		return true;
	};





	/***************/
    /*   LOADING   */
    /***************/
    // set the basic loading listeners
    $rootScope.$on('startLoading', function(e, args)
    {
        $rootScope.loading = true;
    });

     // set the basic loading listeners
    $rootScope.$on('endLoading', function()
    {
        $rootScope.loading = false;
    });

});



/* Load angular when our device is ready */
var onDeviceReady = function()
{   
    // bootstrap angular
    angular.bootstrap(document.querySelector("body#app"), ["app"]);

    // check for cordova
    if(runsNative())
    {
        cordova.plugins.Keyboard.disableScroll(true);
    }
};


/* check if we're running an app or development version */
window.onload = function()
{   
    var app = document.URL.indexOf('http://') === -1 && document.URL.indexOf('https://') === -1;
    if (app) 
    {
        document.addEventListener("deviceready", onDeviceReady, false);
    } 
    else
    {
        onDeviceReady();
    } 
};


