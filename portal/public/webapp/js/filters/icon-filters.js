/*
 * Kweecker iPad app
 * Author: Neat projects <ties@expertees.nl>
 *
 * Icon filters
 */
angular.module('iconFilters', []).filter('makeUrl', function() 
{
  return function(url) {
    return 'img/icons/icon_'+url+'.svg';
  };
});