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

connect = MySQLdb.connect('localhost', 'root', 'abc@123', 'aloabc', use_unicode=True, charset="utf8");
cursor = connect.cursor()

sql = "select crawler_at, id from films where status=1"
cursor.execute(sql)
results = cursor.fetchall()

temp_path = '/mnt/phim.media'

for url in results:
    print url[0]
    soup = GetContent(url[0])
    try:
        link = soup.find('a', {'class': 'btn-watch'}).get('href')
        print link
        type_of_film, link = LinkMP4(link).items()[0]
        filename = '%s/%s-%s.mp4' % (temp_path, type_of_film, url[0].split('/')[-2])
        print filename
        with open(filename, 'wb') as handle:
            response = requests.get(link, stream=True)
            if not response.ok:
                print error
            for block in response.iter_content(1024):
                handle.write(block)
        #break
    except Exception as e:
        print str(e)
        print url[0] + " \t\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"
