#!/usr/bin/env python
# -*- coding: utf-8 -*-

import time

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
