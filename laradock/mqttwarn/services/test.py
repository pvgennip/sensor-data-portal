#!/usr/bin/env python
# -*- coding: utf-8 -*-

import time

def split_ssu_wap_for_influx(tpc, pl):
    out = pl.split(",")
    tpc = "topic=" + tpc.replace('/', '_')
    tst = int(time.time())
    pyl = "%s,sensor_id=%s,type=ssu_wap temp_ssu=%s,temp_wap=%s,pressure_ssu=%s,pressure_wap=%s,bat_v=%s %s" % (tpc, out[0], float(out[1])/10, float(out[4])/10, int(out[2]), int(out[3]), float(out[5])/1000, tst)
    return pyl


# HAP Message:     1494335973, 98, 0, 65140, 52924, 168
#                  0          1   2    3   4   5
# HAP formatting:  Timestamp, CO, CO2, P1, P2, BatteryLevel
def split_hap_sum_for_influx(pl, sensor_id, tp):

    tpc= "topic=" + tp.replace('/'+sensor_id, '').replace('/', '_')
    print 'topic: ' + tpc
    print 'payload length: ' + str(len(pl))
    if len(pl) == 26: # HAP payload 
        out       = []
        out.append(int(pl[0:8], 16)) #ts
        out.append(int(pl[8:12], 16))
        out.append(int(pl[12:16], 16))
        out.append(int(pl[16:20], 16))
        out.append(int(pl[20:24], 16))
        out.append(int(pl[24:26], 16))
        tst       = int(out[0]) if int(out[0]) > 0 else int(time.time())
        pyl       = "%s,sensor_id=%s,type=hap_sum co=%s,co2=%s,p1=%s,p2=%s,hap_bat_v=%s %s" % (tpc, sensor_id, float(out[1]), float(out[2]), float(out[3]), float(out[4]), (float(out[5])+200)/100, tst)
    elif len(pl) == 16: # HAP payload 
        out       = []
        out.append(int(pl[0:8], 16)) # ts
        out.append(int(pl[8:10], 16)) # Dur
        out.append(int(pl[10:14], 16)) # T max
        out.append(int(pl[14:16], 16)) # Bat Level
        tst       = int(out[0]) if int(out[0]) > 0 else int(time.time())
        pyl       = "%s,sensor_id=%s,type=hap_sum duration=%s,t_max=%s,sum_bat_v=%s %s" % (tpc, sensor_id, float(out[1]), float(out[2]), (float(out[3])+200)/100, tst)

    return pyl
    
# tp  = "ssu_wap"
# pl  = "6d70a5d273205cae,203,1024,1017,199,3677"
# tag = ""

tp  = "ITAY/HAP/0000E0DB40604500"
pl  = "81ff0b6e0000000000000000e2"
tag = ""
tpc = tp.split('/')

if tp == "ssu_wap":
    tag = ""
    payload = split_ssu_wap_for_influx(tp, pl)
elif tpc[0] == "ITAY" and tpc[1] == "HAP":
    tag = ""
    payload = split_hap_sum_for_influx(pl, tpc[2], tp)
else:
    payload = " value="+item.payload


print payload

# SSU Message:    6d70a5d273205cae,203,1024,1017,199,3677
# SSU Formatting: identication, SSU temperature (C/10), SSU pressure (hPa), WAP pressure (millibar), WAPtemperature (C/10), battery voltage (mV).
# <measurement>,<tag_key>=<tag_value>,<tag_key>=<tag_value> <field_key>=<field_value>,<field_key>=<field_value> <timestamp>
