#!/usr/bin/env python
# -*- coding: utf-8 -*-
import requests
from bs4 import BeautifulSoup
import MySQLdb

def GetContent(url):
    data = requests.get(url)
    soup = BeautifulSoup(data.content, "html.parser")
    return soup

def InsertLink(cursor, connect, col, value, id):
    sql = "update films set %s='%s' where id=%d" % (col, value, id)
    print sql
    try:
        cursor.execute(sql)
        connect.commit()
    except:
        pass

connect = MySQLdb.connect('localhost', 'root', 'abc@123', 'aloabc', use_unicode=True, charset="utf8");
cursor = connect.cursor()

sql = "select crawler_at, id from films where status=1"
cursor.execute(sql)
results = cursor.fetchall()
for url in results:
    soup = GetContent(url[0])
    title_viet = soup.find('h2', {'class': 'title fr'}).text.strip()
    InsertLink(cursor, connect, "title_viet", title_viet, int(url[1]))

    title_english = soup.find('div', {'class': 'name2 fr'}).text.strip()
    InsertLink(cursor, connect, "title_english", title_english, int(url[1]))

    description = soup.find('div', {'class': 'detail-content-main'}).text.strip()
    InsertLink(cursor, connect, "description", description, int(url[1]))

    # http://www.phim.media/upload/images/phim/phim/diablo-2015.jpg - big images
    # http://www.phim.media/temp/upload/images/phim/phim/diablo-2015.jpg
    thumbnail_id = soup.find('div', {'class':'poster'}).img.get('src')
    InsertLink(cursor, connect, "thumbnail_id", thumbnail_id, int(url[1]))

    infos = soup.findAll('dd')
    country = infos[0].text.strip()
    InsertLink(cursor, connect, "country", country, int(url[1]))

    play_time = infos[1].text.strip()
    InsertLink(cursor, connect, "play_time", play_time, int(url[1]))
