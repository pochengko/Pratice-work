# -*- coding: utf-8 -*-
import urllib3
import MySQLdb as mysql
import os,time,requests,sys,datetime
reload(sys)#重要編碼
from threading import Timer
from datetime import date
from bs4 import *
urllib3.disable_warnings()
sys.setdefaultencoding('utf8')

conn=mysql.connect(host="localhost",user="root",passwd="23590121-33851",db="Env",charset="utf8")
cursor=conn.cursor()
def is_number(s):
    try:
        float(s)
        return s
    except ValueError:
        pass

    try:
        import unicodedata
        unicodedata.numeric(s)
        return s
    except (TypeError, ValueError):
        pass
    s='NA'
    return s

url_voc='https://voc.thu.edu.tw/connect.php'
html=requests.get(url_voc)
html.encoding='utf-8'
sp=BeautifulSoup(html.text,'html.parser')

for i in range(0,36,9):
    PublishTime = sp.find_all('td')[i].string
    SiteName=sp.find_all('td')[i+1].string
    Humidity=sp.find_all('td')[i+2].string
    Illuminance=sp.find_all('td')[i+3].string
    PM1=sp.find_all('td')[i+4].string
    PM10=sp.find_all('td')[i+5].string
    PM25=sp.find_all('td')[i+6].string
    Temperature=sp.find_all('td')[i+7].string
    VOC=sp.find_all('td')[i+8].string

    print PublishTime,SiteName,Humidity,Illuminance,PM1,PM10,PM25,Temperature,VOC

    cursor.execute("SET NAMES UTF8")
    select_sql="select * from voc where `PublishTime`= %s and `SiteName`= %s "
    cursor.execute(select_sql,(PublishTime,SiteName,))
    result=cursor.fetchall()
    conn.commit()
    SiteName = SiteName
    if result==():
        insert_sql="insert into voc(id,SiteName,Humidity,Illuminance,PM1,PM10,PM25,Temperature,VOC,PublishTime) values (null,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
        data= SiteName,Humidity,Illuminance,PM1,PM10,PM25,Temperature,VOC,PublishTime
        cursor.execute(insert_sql,data)
        """
        cursor.execute(
          UPDATE VOC_rt
          SET Humidity=%s,Illuminance=%s,PM1=%,PM10=%s,PM25=%s,Temperature=%s,VOC=%s,PublishTime=%s
          WHERE SiteName=%s
        , (Humidity,Illuminance,PM1,PM10,PM25,Temperature,VOC,PublishTime,SiteName))
        """
        conn.commit()
    else:
        conn.close()
