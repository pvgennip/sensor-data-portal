/*
 * Sensor data portal
 * Author: Pim van Gennip (pim@iconize.nl)
 *
 */


var runsNative = function()
{
	if(typeof cordova == 'undefined')
	{
		return false;
	}

	return true;	
};



var range = function(n)
{
  return new Array(n);
};

var convertOjectToArray = function(obj)
{
  var array = [];
  for (var i in obj) {
    array.push(obj[i]);
  }
  return array;
}

var convertOjectToFormDataArray = function(obj, nameAdd)
{
  if (typeof(nameAdd) == 'undefined')
    nameAdd = '';

  var array = [];
  for (var i in obj) {
    array.push(i+nameAdd+"="+obj[i]);
  }
  return array;
}

// Chart functions
var convertSensorMeasurementsArrayToChartObject = function(obj_arr)
{
  var obj_out = {data:[], labels:[], series:[]};
  var series_index = -1;
  for (var i = 0; i < obj_arr.length; i++)
  {
    var m = obj_arr[i];
    if (obj_out.series.indexOf(m.name) == -1)
    {
      series_index ++;
      obj_out.series.push(m.name);
      //obj_out.series[series_index] = m.name;
      //obj_out.data[series_index] = [];
      //obj_out.labels[series_index] = [];
    }
    obj_out.data.push(m.value);
    obj_out.labels.push(m.time);
  }
  return obj_out;
}

var solidColorObj = function(rgbaStr, borderRgbaStr) // This is the only way to pass a losid color value, RGB values get converted to alpha 0.2 in angular-chart.js
{
    cObj = {
        backgroundColor: rgbaStr,
        pointBackgroundColor: rgbaStr,
        pointHoverBackgroundColor: rgbaStr
    };
    
    if (borderRgbaStr)
    {
        cObj.borderColor = borderRgbaStr;
        cObj.pointBorderColor = borderRgbaStr;
        cObj.pointHoverBorderColor = borderRgbaStr;
    }

    return cObj;
};


// Settings
var convertSettingJsonToObject = function(json)
{
  var out = {};
  for (var i in json) {
    var o = json[i];
    if (o.name != "")
      out[o.name] = o.value;
  }
  return out;
}

var percDiffOf = function(tot, num)
{
  return tot > 0 ? (Math.abs(tot - num) / tot) * 100 : num > 0 ? 100 : 0;
}

var number_format = function(number, decimals, decPoint, thousandsSep) 
{
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
  var n = !isFinite(+number) ? 0 : +number
  var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
  var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
  var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
  var s = ''

  var toFixedFix = function (n, prec) {
    var k = Math.pow(10, prec)
    return '' + (Math.round(n * k) / k)
      .toFixed(prec)
  }

  // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || ''
    s[1] += new Array(prec - s[1].length + 1).join('0')
  }

  return s.join(dec)
};




var versionCompare = function(v1, v2, options) 
{
    var lexicographical = options && options.lexicographical,
        zeroExtend = options && options.zeroExtend,
        v1parts = v1.split('.'),
        v2parts = v2.split('.');

    var isValidPart = function(x) {
        return (lexicographical ? /^\d+[A-Za-z]*$/ : /^\d+$/).test(x);
    }

    if (!v1parts.every(isValidPart) || !v2parts.every(isValidPart)) {
        return NaN;
    }

    if (zeroExtend) {
        while (v1parts.length < v2parts.length) v1parts.push("0");
        while (v2parts.length < v1parts.length) v2parts.push("0");
    }

    if (!lexicographical) {
        v1parts = v1parts.map(Number);
        v2parts = v2parts.map(Number);
    }

    for (var i = 0; i < v1parts.length; ++i) {
        if (v2parts.length == i) {
            return 1;
        }

        if (v1parts[i] == v2parts[i]) {
            continue;
        }
        else if (v1parts[i] > v2parts[i]) {
            return 1;
        }
        else {
            return -1;
        }
    }

    if (v1parts.length != v2parts.length) {
        return -1;
    }

    return 0;
}