#!/usr/bin/env python
# -*- coding: utf-8 -*-

__author__    = 'Pim van Gennip <pim@iconize.nl>'
__copyright__ = 'Copyright 2017 Pim van Gennip'
__license__   = """Eclipse Public License - v 1.0 (http://www.eclipse.org/legal/epl-v10.html)"""

import requests
import logging
import time

# disable info logging in requests module (e.g. connection pool message for every post request)
logging.getLogger("requests").setLevel(logging.WARNING)


# SSU Message:    6d70a5d273205cae,203,1024,1017,199,3677
#                 0             1                       2                   3                        4                      5
# SSU Formatting: identication, SSU temperature (C/10), SSU pressure (hPa), WAP pressure (millibar), WAPtemperature (C/10), battery voltage (mV).
# <measurement>,<tag_key>=<tag_value>,<tag_key>=<tag_value> <field_key>=<field_value>,<field_key>=<field_value> <timestamp>
def split_ssu_wap_for_influx(item):
    out = item.payload.strip().split(",")
    tpc = "topic=" + item.topic.replace('/', '_')
    tst = int(time.time())
    val = float( (int(out[3]) - int(out[2]) )/98.1) # Depth value (m) = (wap - ssu)/98.1
    pyl = "%s,sensor_id=%s,type=ssu_wap depth=%s,temp_ssu=%s,temp_wap=%s,pressure_ssu=%s,pressure_wap=%s,bat_v=%s %s" % (tpc, out[0], val, float(out[1])/10, float(out[4])/10, int(out[2]), int(out[3]), float(out[5])/1000, tst)
    return pyl

# HEX payload      0e6c1a5b    b300 0000 2a8b   9sf6   ce               
# HAP Message:     1528458254, 179, 0,   35626, 63125, 4.06
#                  0           1    2    3      4      5
# HAP formatting:  Timestamp,  CO,  CO2, P1,    P2,    BatteryLevel
def split_hap_sum_for_influx(item, sensor_id):
    pl = item.payload # charcter string
    tpc= "topic=" + item.topic.replace('/'+sensor_id, '').replace('/', '_')

    if len(pl) == 26: # HAP payload 
        out       = []
        out.append(int(pl[6:8]+pl[4:6]+pl[2:4]+pl[0:2], 16)) #0 ts (byteswapped)
        out.append(int(pl[10:12]+pl[8:10], 16))  #1 CO   (byteswapped)
        out.append(int(pl[14:16]+pl[12:14], 16)) #2 CO2 (byteswapped)
        out.append(int(pl[18:20]+pl[16:18], 16)) #3 P1  (byteswapped)
        out.append(int(pl[22:24]+pl[20:22], 16)) #4 P2  (byteswapped)
        out.append(int(pl[24:26], 16))           #5 Bat
        tst       = int(out[0]) if int(out[0]) > 0 and int(out[0]) < int(time.time()) else int(time.time())
        pyl       = "%s,sensor_id=%s,type=hap_sum co=%s,co2=%s,p1=%s,p2=%s,hap_bat_v=%s %s" % (tpc, sensor_id, float(out[1]), float(out[2]), float(out[3]), float(out[4]), (float(out[5])+200)/100, tst)
    elif len(pl) == 16: # SUM payload 
        out       = []
        out.append(int(pl[6:8]+pl[4:6]+pl[2:4]+pl[0:2], 16)) # ts   (byteswapped)
        out.append(int(pl[8:10], 16))                        # Dur
        out.append(int(pl[12:14]+pl[10:12], 16))             # T max (byteswapped)
        out.append(int(pl[14:16], 16))                       # Bat Level
        tst       = int(out[0]) if int(out[0]) > 0 and int(out[0]) < int(time.time()) else int(time.time())
        pyl       = "%s,sensor_id=%s,type=hap_sum duration=%s,t_max=%s,sum_bat_v=%s %s" % (tpc, sensor_id, float(out[1]), float(out[2]), (float(out[3])+200)/100, tst)

    return pyl

        
# item = {
#    'service'       : 'string',       # name of handling service (`twitter`, `file`, ..)
#    'target'        : 'string',       # name of target (`o1`, `janejol`) in service
#    'addrs'         : <list>,         # list of addresses from SERVICE_targets
#    'config'        : dict,           # None or dict from SERVICE_config {}
#    'topic'         : 'string',       # incoming topic branch name
#    'payload'       : <payload>       # raw message payload
#    'message'       : 'string',       # formatted message (if no format string then = payload)
#    'data'          : (only if JSON payload is detected),           # dict with transformation data
#       {
#         'topic'         : topic name
#         'payload'       : topic payload
#         '_dtepoch'      : epoch time                  # 1392628581
#         '_dtiso'        : ISO date (UTC)              # 2014-02-17T10:38:43.910691Z
#         '_dthhmm'       : timestamp HH:MM (local)     # 10:16
#         '_dthhmmss'     : timestamp HH:MM:SS (local)  # 10:16:21
#       }
#    'title'         : 'mqttwarn',     # possible title from title{}
#    'priority'      : 0,              # possible priority from priority{}
# }

def plugin(srv, item):
    ''' addrs: (measurement) '''

    srv.logging.debug("*** MODULE=%s: service=%s, target=%s", __file__, item.service, item.target)

    host        = item.config['host']
    port        = item.config['port']
    username    = item.config['username']
    password    = item.config['password']
    database    = item.config['database']

    measurement = item.addrs[0]
    tag         = "topic=" + item.topic.replace('/', '_')
    
    if item.target == "ssu":
        tag = ""
        payload = split_ssu_wap_for_influx(item)
    elif item.target == "hap":
        tag      = ""
        topicArr = item.topic.split('/')
        deviceId = topicArr[2] if len(topicArr) > 2 else '-' 
        payload  = split_hap_sum_for_influx(item, deviceId)
    else:
        payload = " value="+item.payload

    srv.logging.debug("Measurement: %s, Payload: %s", measurement, payload)
    
    try:
        url = "http://%s:%d/write?db=%s&precision=s" % (host, port, database)
        data = measurement + ',' + tag + payload

        srv.logging.debug("Data to be send to Influx: %s" % (data))
        
        if username is None:
            r = requests.post(url, data=data)
        else:
            r = requests.post(url, data=data, auth=(username, password))
        
        # success
        if r.status_code == 204:
            return True
            
        # request accepted but couldn't be completed (200) or failed (otherwise)
        if r.status_code == 200:
            srv.logging.warn("POST request could not be completed: %s" % (r.text))
        else:
            srv.logging.warn("POST request failed: (%s) %s" % (r.status_code, r.text))
        
    except Exception, e:
        srv.logging.warn("Failed to send POST request to InfluxDB server using %s: %s" % (url, str(e)))

    return False




