/*
 * Sensor data portal
 * Author: Pim van Gennip (pim@iconize.nl)
 *
 */


app.config(['$routeProvider', '$locationProvider',  function($routeProvider) 
{
	
  $routeProvider

 // login
  .when('/login', {
      controller:'UserCtrl',
      templateUrl:'views/forms/login.html'
  })

  // login/create
  .when('/login/create', {
        controller:'UserCtrl',
        templateUrl:'views/forms/user/create.html'
  })

    // login/reminder
  .when('/login/reminder', {
        controller:'PasswordCtrl',
        templateUrl:'views/forms/user/reminder.html'
  })

    // login/reset
  .when('/login/reset', {
        controller:'PasswordCtrl',
        templateUrl:'views/forms/user/reset.html'
  })

  // logout
  .when('/logout', {
      controller:'UserCtrl',
      templateUrl:'views/forms/logout.html'
  })

  // load
  .when('/load', {
      controller:'LoadCtrl',
      templateUrl:'views/loading.html'
  })


  // overview
  .when('/dashboard',
  {
      controller  : 'DashboardCtrl',
      templateUrl : 'views/dashboard.html',
  })


  // settings
 .when('/settings', 
 {
        controller:'SettingsCtrl',
        templateUrl:'views/forms/settings.html'
 })


  // none...
  .otherwise(
  {
    redirectTo : '/load'
  });


}]);