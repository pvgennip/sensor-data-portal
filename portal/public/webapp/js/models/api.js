/*
 * Kweecker iPad app
 * Author: Neat projects <ties@expertees.nl>
 *
 * API model
 */
app.service('api', ['$http', '$rootScope', function($http, $rootScope)
{

	var self    	   = this;

	this.token  	   = null;

	this.getApiToken = function()
	{
		// get the api token
		if(localStorage.getItem('app_api_token'))
		{
			self.token = localStorage.getItem('app_api_token');
		}
		return self.token;
	};


	this.setApiToken = function(token)
	{
		// set the api token
		if(token != null)
		{
			localStorage.setItem('app_api_token', token);
			self.token = token;
		}
	};


	this.removeApiToken = function()
	{
		// remove from the storage
		localStorage.removeItem('app_api_token');
		// remove from memory
		self.token = null;
	};


	this.reset = function()
	{
		// api token
		self.removeApiToken();
	};




	this.registerUser = function(password, email)
	{
		var data = 
		{
			password	  : password,
			email		  : email
		};

		self.postApiRequest('register', 'register', data);
	};



	this.login = function(email, password)
	{
		var credentials = 
		{
			email 	 : email,
			password : password
		};

		self.postApiRequest('login', 'login', credentials);
	};

	this.authenticate = function()
	{
		self.postApiRequest('authenticate', 'authenticate');
	};


	this.cache = {};

	this.passwordReminder = function(email)
	{
		self.cache.email = email;

		self.postApiRequest('passwordReminder', 'user/reminder', {email : email});
	};


	this.passwordReset = function(email, password, password_confirm, token)
	{
		var credentials = 
		{
			email 			 : email,
			password 		 : password,
			password_confirm : password_confirm,
			token 			 : token
		};

		self.postApiRequest('passwordReset', 'user/reset', credentials);
	};





	this.deleteApiRequest = function(type, request, data, params)
	{
		self.postApiRequest(type, request, data, params, 'DELETE');
	};


	this.putApiRequest = function(type, request, data, params)
	{
		self.postApiRequest(type, request, data, params, 'PUT');
	};


	this.postApiRequest = function(type, request, data, params, method)
	{
		var params = typeof params !== 'undefined' ? params+'&' : '';
		var method = typeof method !== 'undefined' ? method : 'POST';

		var url    = API_URL+request
		url += params == '' ? '' : '?'+params;

		// set the request
		var req = 
		{
			method  : method,
			headers : 
			{
    			'Content-Type'  : 'application/json',
 			},
 			data : data,
			url  : url,
		};

		// check if it has to be authorized
		if(type != 'login' && type != 'register' && self.getApiToken() != null)
		{
			req.headers['Authorization'] = 'Bearer '+self.getApiToken()+'';
		}

		// do the request
		self.doApiRequest(type, req);
	};


	this.getApiRequest = function(type, request, count, offset, params)
	{
		var params = typeof params !== 'undefined' ? params+'&' : '';
		var count  = (typeof count !== 'undefined') ? count : 0;
		var offset = (typeof offset !== 'undefined') ? offset : 0;
		var url    = API_URL+request+'?'+params+'count='+count+'&offset='+offset+'';

		// set the request
		var req = 
		{
			method  : 'GET',
			headers : 
			{
    			'Content-Type'  : 'application/json',
 			},
			url  : url,
		};

		// check if it has to be authorized
		if(type != 'register' && self.getApiToken() != null)
		{
			req.headers['Authorization'] = 'Bearer '+self.getApiToken()+'';
		}

		// do the request
		self.doApiRequest(type, req);
	}




	this.doApiRequest = function(type, req)
	{
		// start loading
		$rootScope.$broadcast('startLoading');

		// set a request timeout
		//req.timeout = (PING_FREQ_CONNECTED-1000);

		// do the request
		$http(req).then(
			function(response) // success
			{
				// set the data
				var result = (response.data != undefined) ? response.data : response;

				// set the listeners
				$rootScope.$broadcast(type+'Loaded', result);
				$rootScope.$broadcast('endLoading');
			}
			, function(response) // error
			{
				var error = (response != undefined) ? (response.data != undefined) ? (response.data.message != undefined) ? response.data.message : response.data : response : 'error';
				var status= (response != undefined) ? response.status : 0;
				// set the listeners
				$rootScope.$broadcast(type+'Error', {'message':error, 'status':status});
				$rootScope.$broadcast('endLoading');

				if(status == 401) // re-authenticate
				{
					if (self.token != null)
					{
						$rootScope.showMessage($rootScope.lang.no_valid_authentication, null, $rootScope.lang.login_title);
						removeApiToken();
					}
					$rootScope.goToPage.path('/login');
				}
			}
		);	
		
	};


	self.getApiToken();
}]);