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

def link(url)
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
        return temp_mp4['HD']
    except:
        return temp_mp4['SD']

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
for url in results:
    soup = GetContent(url[0])
    title_viet = soup.find('h2', {'class': 'title fr'}).text
    InsertLink(cursor, connect, "title_viet", title_viet, int(url[1]))

    title_english = soup.find('div', {'class': 'name2 fr'}).text
    InsertLink(cursor, connect, "title_english", title_english, int(url[1]))

    description = soup.find('div', {'class': 'detail-content-main'}).text
    InsertLink(cursor, connect, "description", description, int(url[1]))

    # http://www.phim.media/upload/images/phim/phim/diablo-2015.jpg - big images
    # http://www.phim.media/temp/upload/images/phim/phim/diablo-2015.jpg
    thumbnail_id = link(soup.find('div', {'class':'poster'}).img.get('src'))
    InsertLink(cursor, connect, "thumbnail_id", thumbnail_id, int(url[1]))

    infos = soup.findAll('dd')
    country = infos[0].text.strip()
    InsertLink(cursor, connect, "country", country, int(url[1]))

    play_time = infos[1].text.strip()
    InsertLink(cursor, connect, "play_time", play_time, int(url[1]))
