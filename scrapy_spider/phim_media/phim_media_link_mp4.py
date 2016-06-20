#!/usr/bin/env python
# -*- coding: utf-8 -*-
import requests
from bs4 import BeautifulSoup
import MySQLdb

def GetContent(url):
    data = requests.get(url)
    soup = BeautifulSoup(data.content, "html.parser")
    return soup

def InsertLink(cursor, connect, col, value):
    sql = "update films set %s='%s' where id=%d" % (col, value, id)
    print sql
    try:
        cursor.execute(sql)
        connect.commit()
    except:
        pass

def LinkMP4(url):
    # should check the link_film.txt for structure
    # return sd/hd link of film
    data = requests.get(url)

    soup = BeautifulSoup(data.content, "html.parser")
    mp4 = soup.find('video')
    links = mp4.findAll(src=True)
    temp_mp4 = {}
    for i in links:
        if 'googleusercontent' in i.get('src'):
            temp_mp4.update({i.get('data-res') : i.get('src')})
    try:
        return {'HD' : temp_mp4['HD']}
    except:
        return {'SD': temp_mp4['SD']}

def Info(Tag):
    # should check the info_link_img_film.txt for structure
    # link get info http://www.phim.media/phim-vien-tay-dam-mau/
    # link autoplay http://www.phim.media/phim-vien-tay-dam-mau/xem-online.html
    img = Tag.img.get('src')
    info = Tag.a.get('href')
    return {}

connect = MySQLdb.connect('localhost', 'root', 'abc@123', 'aloabc', use_unicode=True, charset="utf8");
cursor = connect.cursor()

sql = "select crawler_at, id from films where status=1"
cursor.execute(sql)
results = cursor.fetchall()
count = 1
for url in results:
    soup = GetContent(url[0])
    try:
        link = soup.find('a', {'class': 'btn-watch'}).get('href')
        print link
        print LinkMP4(link)
        count += 1
        if count > 100:
            break
    except:
        print url[0] + " \t\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"
