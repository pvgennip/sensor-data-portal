/*
 * Sensor data portal
 * Author: Pim van Gennip (pim@iconize.nl)
 *
 */
 LANG['en'] = 
 {

 	/* main */
 	back					: 'Back',
 	menu					: 'Menu',
 	lighting				: 'Lighting',
 	camera					: 'Camera',
    weather                 : 'Weather',
 	sensors					: 'Sensors',

 	no_valid_authentication : 'No valid authentication data received',
 	succesfully_saved		: 'Succesfully saved',

 	remove_all_settings 	: 'Reset',

    /* user error messages */
    username_is_required    : 'Please enter the username',
    username_already_exists : 'Username already exists',
    password_is_required    : 'Please enter a password',
    email_is_required       : 'Please enter a e-mailadres',
    email_already_exists    : 'The e-mailaddress is already in use',

    already_registered      : 'I am already registered',
    invalid_user            : 'Unable to find user',
    invalid_password        : 'Password too short (min. 8 characters)',
    invalid_token           : 'Invalid code',

    no_valid_email          : 'Invalid e-mailaddress',

    empty_fields            : 'Please fill in all the fields',
    match_passwords         : 'Passwords do not match',

    succesfully_registered  : 'You are succesfully registered',
    authentication_failed   : 'Failed to authenticate',

    no_valid_input_received : 'Data could not be saved, no valid input received.',

 	ok 						: 'Ok',
    previous                : 'Previous',
 	prev	     			: 'previous',
 	next					: 'Next',
 	add						: 'Add',

 	warning					: 'Warning',

 	apply					: 'Apply',
 	automatic				: 'Automatic',
 	manually				: 'Manual',
 	on 						: 'On',
 	off						: 'Off',

 	shutdown 				: 'Shutdown Hive',
    controller_shutdown     : 'Hive is shutting down',
 	controller_reboot       : 'Hive is being reboot',

 	/* login */
 	login_title 			: 'Login to enter the portal',
 	login 					: 'Login',
    back_to_login           : 'Back to login',
    forgot_password         : 'Forgot your password?',

 	username				: 'Username',
 	password				: 'Password',
 	confirm_password		: 'Confirm password',
 	email					: 'E-mail',
 	token                   : 'Verification code',

    create_login_question   : 'No account yet? Register as a new user',
    create_login            : 'Register as a new user',
    create_login_summary    : 'Create a new user account',
    save                    : 'Save',

    logout                  : 'Log out',
    logout_title            : 'Log out as ',
    logout_now              : 'Do you realy want to log out now?',

    /* password recovery */
    password_recovery_title            : 'Forgot your password?',
    password_recovery_remembered       : 'Oh, now I remembered my password again!',
    password_recovery_user             : 'User information',
    password_recovery_send_mail        : 'Send verification code',
    password_recovery_code_not_received: 'Code not received within 5 minutes?',
    password_recovery_enter_code       : 'Already got a verification code? Enter it here',
    password_recovery_reset_title      : 'Enter a new password',
    password_recovery_reset_password   : 'Change password',
    password_recovery_reminder_success : 'An e-mail has been sent. Copy the code from your e-mail and paste it here.',
    password_recovery_reminder_summary : 'Enter your e-mail address. You will receive a verification code to change your password in the next step.',
    
    password_recovery_reset_summary    : 'Use the code that you received to set a new password for your account',
    password_recovery_reset_success    : 'You passowrd is successfully changed, and you are logged in.',

    new_password                       : 'New password',
    confirm_new_password               : 'Confirm new password',

    go_to_dashboard                    : 'Go to my dashboard',

 	 /* overview */
 	overview_title 			: 'Overview',
    overview_description    : 'Overview over the status of the sensors',
 	overview 				: 'Overview',
 	color 					: 'Color',
 	state 					: 'On/off',
 	climate					: 'Climate',
 	plant_state 			: 'Plant status',
 	connection_state 		: 'Connection status',

 	/* dashboard */
 	dashboard_title 		: 'Dashboard',
    dashboard_description   : 'Dashboard of your sensors',
 	dashboard 				: 'Dashboard',
    measurements            : 'Measurements',
    measurementsError       : 'Cannot load measurements, check network connection',
    last_measurement_was    : 'Last recorded measurement was',
    at                      : 'at',

 	/* settings */
 	settings_title			: 'Settings',
    settings_description    : 'Settings of the sensors',
 	settings 				: 'Settings',

    sensors_title           : 'Sensors overview',
    sensors_description     : 'Sensors status and registration',
    sensors                 : 'Sensors',

    data_analysis            : 'Data analysis',
    data_analysis_title      : 'Data analysis',
    data_analysis_description: 'Graphical sensor data analysis',

 	climate_settings_title	: 'Climate settings',
 	climate_settings    	: 'Climate settings',
 	night				    : 'Night',	
 	morning					: 'Morning',
 	noon					: 'Noon',
 	afternoon				: 'Aftrnoon',
    climate_settings        : 'Climate settings',
    reboot                  : 'Reboot',
    min                     : 'Minimum x per day',
    max                     : 'Maximum x per day',
    startMinAfterSunRise    : 'Start min after sunrise',
    stopMinAfterSunSet      : 'Stop min after sunset',


 	/* plants */
 	select_plant			: 'Select plant',

    contents_succesfully_added : 'Plant successfully added',
    contents_failed            : 'Failed to add plant',
    contents                   : 'Plants',

 	/* colors */
 	select_color 			: 'Select a color',
 	could_not_set_color  	: 'Could not set the lighting colors',

 	/* camera */
 	could_not_take_picture 	: 'Could not take a picture',
 	camera_setup			: 'Camera setup',
 	view_last_video			: 'View video',
 	view_last_photo			: 'View photos',
 	live_camera_view 		: 'Live image',

    /* light amounts */
    sun_lux_dark            : 'dark',
    sun_lux_dusk            : 'dusk',
    sun_lux_low             : 'low light',
    sun_lux_cloudy          : 'cloudy',
    sun_lux_half            : 'partly cloudy',
    sun_lux_sunny           : 'sunny',

 	/* plant */
    plants                  : 'Crops',
 	choose_plant_position	: 'Choose plant position',
 	choose_plant_state		: 'Choose the current state of the plant',

    advanced                : 'Advanced',


 	/* actuators */
 	temperature 			: 'Temperature',
 	light 					: 'Sunlight',
 	water 					: 'Water',
 	humidity 				: 'Humidity',
    air_pressure            : 'Air pressure',
    weight                  : 'Weight',

    /* settings */
    could_not_load_settings : 'Settings could not be loaded',

 };