/*
 * Kweecker iPad app
 * Author: Neat projects <ties@expertees.nl>
 *
 * Text filters
 */
angular.module('textFilters', []).filter('removeDot', function() 
{
  return function(str) {
    return str.replace('.', '');
  };
});