# functions.py for mqttwarn
#
# item = {
#    'service'       : 'string',       # name of handling service (`twitter`, `file`, ..)
#    'target'        : 'string',       # name of target (`o1`, `janejol`) in service
#    'addrs'         : <list>,         # list of addresses from SERVICE_targets
#    'config'        : dict,           # None or dict from SERVICE_config {}
#    'topic'         : 'string',       # incoming topic branch name
#    'payload'       : <payload>       # raw message payload
#    'message'       : 'string',       # formatted message (if no format string then = payload)
#    'data'          : (only if JSON payload is detected),           # dict with transformation data
#		{
#		  'topic'         : topic name
#		  'payload'       : topic payload
#		  '_dtepoch'      : epoch time                  # 1392628581
#		  '_dtiso'        : ISO date (UTC)              # 2014-02-17T10:38:43.910691Z
#		  '_dthhmm'       : timestamp HH:MM (local)     # 10:16
#		  '_dthhmmss'     : timestamp HH:MM:SS (local)  # 10:16:21
#		}
#    'title'         : 'mqttwarn',     # possible title from title{}
#    'priority'      : 0,              # possible priority from priority{}
# }


# Topic: ITAY/SSU
# Message: 6d70a5d273205cae,203,1024,1017,199,3677
#             [0]           [1]                     [2]                 [3]                      [4]                    [5]
# Formatting: identication, SSU temperature (C/10), SSU pressure (hPa), WAP pressure (millibar), WAPtemperature (C/10), battery voltage (mV).

def split_ssu_wap_for_influx(data, srv=None):
    out = data.payload.split(",")
    return data.topic,",sensor_id=",out[0],",type=ssu_wap temp_ssu=",out[1]/10," temp_wap=",out[4]/10," pressure_ssu=",out[2]," pressure_wap=",out[3]," bat_v=",out[5]/1000

def split_hap_sum_for_influx(data, srv=None):
    return data

