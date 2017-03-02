/*
 * Sensor data portal
 * Author: Pim van Gennip (pim@iconize.nl)
 *
 */
 LANG['nl'] = 
 {

 	/* main */
 	back					: 'Terug',
 	menu					: 'Menu',
 	lighting				: 'Sfeerverlichting',
 	camera					: 'Camera',
 	weather					: 'Weer',
    sensors                 : 'Sensoren',

 	no_valid_authentication : 'Geen geldige login ontvangen',
 	succesfully_saved		: 'Succesvol opgeslagen',

    remove_all_settings     : 'Verwijder app data',

    /* user error messages */
    username_is_required     : 'Vul een gebruikersnaam in.',
    username_already_exists  : 'De gebruikersnaam is al in gebruik',
    password_is_required     : 'Vul een wachtwoord in.',
    email_is_required        : 'Vul een e-mailadres in',
    email_already_exists     : 'Het e-mailadres is al in gebruik',

    already_registered      : 'Ik heb al een account',
    invalid_user             : 'Gebruiker is niet gevonden',
    invalid_password         : 'Wachtwoord te kort (min. 8 tekens)',
    invalid_token            : 'Ongeldige code',

    no_valid_email           : 'Ongeldig e-mailadres',

    empty_fields             : 'U heeft niet alle velden goed ingevuld.',
    match_passwords          : 'De wachtwoorden komen niet overeen.',

    succesfully_registered   : 'Je bent succesvol geregistreerd en direct ingelogd.',
    authentication_failed    : 'Inloggen niet gelukt',

    no_valid_input_received  : 'Data kon niet worden opgeslagen, geen geldige gegevens.',

 	ok 						: 'Ok',
    previous                : 'Vorige',
 	prev      				: 'vorige',
 	next					: 'Volgende',
 	add						: 'Toevoegen',

 	warning					: 'Let op',

 	apply					: 'Toepassen',
 	automatic				: 'Automatisch',
 	manually				: 'Handmatig',
 	on 						: 'Aan',
 	off						: 'Uit',

 	shutdown 					: 'Kast uitzetten',
    controller_shutdown     	: 'Kast wordt uitgezet',
 	controller_reboot           : 'Kast wordt herstart',

 	/* login */
 	login_title             : 'Inloggen',
    login                   : 'Aanmelden',
    back_to_login           : 'Terug naar inloggen',
    forgot_password         : 'Wachtwoord vergeten?',

    username                : 'Gebruikersnaam',
    password                : 'Wachtwoord',
    confirm_password        : 'Bevestig wachtwoord',
    email                   : 'E-mail',
    token                   : 'Code',

    create_login_question   : 'Nog geen account? Registreer als een nieuwe gebruiker',
    create_login            : 'Registreer als een nieuwe gebruiker',
    create_login_summary    : 'Creeër een nieuw account',
    save                    : 'Opslaan',

    logout                  : 'Uitloggen',
    logout_title            : 'Uitloggen als ',
    logout_now              : 'Weet je zeker dat je wil uitloggen?',

    /* password recovery */
    password_recovery_title            : 'Wachtwoord vergeten?',
    password_recovery_remembered       : 'Oh wacht, ik weet mijn wachtwoord weer!',
    password_recovery_user             : 'Gebruikersinformatie',
    password_recovery_send_mail        : 'Verstuur code',
    password_recovery_code_not_received: 'Code niet ontvangen binnen 5 minuten?',
    password_recovery_enter_code       : 'Voer de ontvangen code in',
    password_recovery_reset_title      : 'Stel een nieuw wachtwoord in',
    password_recovery_reset_password   : 'Verander wachtwoord',
    password_recovery_reminder_success : 'Er is een e-mail verstuurd, kopieer de code en voer deze in om uw wachtwoord opnieuw in te stellen.',
    password_recovery_reminder_summary : 'Vul je e-mailadres in. Je ontvangt vervolgens een code waarmee je een nieuw wachtwoord kunt instellen in de volgende stap.',
    
    password_recovery_reset_summary    : 'Gebruik de ontvangen code om een nieuw wachtwoord voor je account in te stellen',
    password_recovery_reset_success    : 'Je wachtwoord is succesvol aangepast, je bent nu ingelogd.',

    new_password                       : 'Nieuw wachtwoord',
    confirm_new_password               : 'Bevestig nieuw wachtwoord',

    go_to_dashboard                    : 'Ga direct naar het overzicht',

 	/* overview */
 	overview_title 			: 'Overzicht',
 	overview 				: 'Overzicht',
 	color 					: 'Kleur',
 	state 					: 'Stand',
 	climate					: 'Klimaatregeling',
 	plant_state 			: 'Status planten',
 	connection_state 		: 'Status verbinding',

 	/* dashboard */
 	dashboard_title 		: 'Dashboard',
 	dashboard 				: 'Dashboard',
    measurements            : 'Metingen',
    measurementsError       : 'Kan geen metingen laden, controleer de netwerkverbinding',
    last_measurement_was    : 'De laatste meting was',
    at                      : 'op',

 	/* settings */
 	settings_title			: 'Settings',
 	settings 				: 'Instellingen',

 	climate_settings_title	: 'Klimaat instellingen',
 	climate_settings    	: 'Klimaat instellingen',
 	night				    : 'Nacht',	
 	morning					: 'Voor dag',
 	noon					: 'Dag',
 	afternoon				: 'Na dag',
    climate_settings        : 'Klimaatinstellingen',
    reboot                  : 'Herstart',
    min                     : 'Minimum x per dag',
    max                     : 'Maximum x per dag',
    startMinAfterSunRise    : 'Start min na zon op',
    stopMinAfterSunSet      : 'Stop min na zon onder',

 	/* plants */
 	select_plant			: 'Selecteer een nieuwe plant',

    contents_succesfully_added : 'Plant is toegevoegd',
    contents_failed            : 'Plant toevoegen is niet gelukt.',
    contents                   : 'Planten',
 
 	/* colors */
 	select_color 			: 'Selecteer een kleur',
 	could_not_set_color  	: 'De verlichting kon niet ingesteld worden',

 	/* camera */
 	could_not_take_picture 	: 'Er kon geen foto worden gemaakt',
 	camera_setup			: 'Stel de camera in',
 	view_last_video			: 'Bekijk video',
 	view_last_photo			: 'Bekijk foto\'s',
 	live_camera_view 		: 'Live beeld',

    /* light amounts */
    sun_lux_dark            : 'donker',
    sun_lux_dusk            : 'schemer',
    sun_lux_low             : 'weinig zon',
    sun_lux_cloudy          : 'bewolkt',
    sun_lux_half            : 'half bewolkt',
    sun_lux_sunny           : 'zonnig',

 	/* plant */
    plants                  : 'Gewassen',
 	choose_plant_position	: 'Kies de positie van de plant',
 	choose_plant_state		: 'Kies het huidige stadium van de plant',

    advanced                : 'Geavanceerd',


 	/* actuators */
 	temperature 			: 'Temperatuur',
 	light 					: 'Zonlicht',
 	water 					: 'Water',
    humidity                : 'Luchtvochtigheid',
    air_pressure            : 'Luchtdruk',
    weight                  : 'Gewicht',
    weight_kg               : 'Gewicht',
 	bat_volt		        : 'Batterij V',

    /* settings */
    could_not_load_settings : 'De instellingen konden niet worden geladen',
    offline                 : 'Geen verbinding',
    remote                  : 'Op afstand',
    connected               : 'Direct',

    yes                     : 'Ja',
    no                      : 'Nee',

    plant_messages          : {
        200 : 'Er zijn geen problemen gedetecteerd',
    }

 };



