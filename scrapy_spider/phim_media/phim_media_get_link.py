#!/usr/bin/env python
# -*- coding: utf-8 -*-
import requests
from bs4 import BeautifulSoup
import MySQLdb

def InsertLink(link, cursor, connect):
    sql = "insert into films(crawler_at) values('%s')" % link
    print sql
    try:
        cursor.execute(sql)
        connect.commit()
        return True
    except MySQLdb.IntegrityError:
        # error with duplicate record, stop crawl next page
        return False

def GetContent(url):
    data = requests.get(url)
    soup = BeautifulSoup(data.content, "html.parser")
    return soup

# define connection for DB
connect = MySQLdb.connect('localhost', 'root', 'abc@123', 'aloabc');
cursor = connect.cursor()

url_page_1 = 'http://www.phim.media/phim-le/'
soup = GetContent(url_page_1)

flag = True
# the element is Tag
first_page_link_films = soup.findAll('div', {'class': 'inner'})
for i in first_page_link_films:
    try:
        print i
        flag = InsertLink(i.a.get('href'), cursor, connect)
        if not flag:
            print "duplicate error"
            break
    except:
        pass

if flag:
    # get all pages
    pages = soup.find('div', {'class': 'Paging'})
    # get last_page digit from string - http://www.phim.media/phim-le-trang-55/
    # convert unicode into string - string.encode('utf-8')
    last_page = int(filter(str.isdigit, pages.findAll('a')[-1]['href'].encode('utf-8')))

    for i in range(2,last_page+1):
        url = 'http://www.phim.media/phim-le-trang-%d/' % i
        soup = GetContent(url)
        # the element is Tag
        link_films = soup.findAll('div', {'class': 'inner'})
        for i in link_films:
            print i
            try:
                InsertLink(i.a.get('href'), cursor, connect)
                if temp:
                    break
            except:
                pass
