# -*- coding: utf-8 -*-
from __future__ import division
import MySQLdb
from datetime import datetime
from subprocess import Popen, PIPE
import shlex
import urllib
import facebook
import imageio
import os

db = MySQLdb.connect("localhost", "root", "", "yii", use_unicode=True, charset="utf8")
cursor = db.cursor()

img_path = '/home/hadn/Downloads'
img_path_edit = '/home/hadn/Downloads/scripts'
fb_token_key = 'EAAYWrk8vdiEBAAa2mohGS1lkaucidjQpyVc1ML54diwg26dT7bRRLNV1FXl5absSo0xKdZA7AjYUi1uE8yRSDdXKJHiSpOZCfvsTabT7EZA9mpTp4csiMtyycCKQM7RRmabIDpbI5l5oYDEOS8RnzSz4IgvkXMZD'
graph = facebook.GraphAPI(access_token=fb_token_key)

def img_normalize(temp_path, img_path_ffmpeg):
    im = imageio.imread(temp_path)
    img_h, img_w, img_v = im.shape
    print img_h, img_w
    if img_w/img_h > 0.66666:
        value = img_h*2/3
        value_crop = img_w - value
        cmd = '/usr/bin/ffmpeg -i %s -vf "crop=%s:ih:%s/2:ih/2" %s -y' % (temp_path, value, value_crop, img_path_ffmpeg)
    else:
        value = img_w*3/2
        print value
        value_crop = img_h - value
        cmd = '/usr/bin/ffmpeg -i %s -vf "crop=iw:%s:iw/2:%s/2" %s -y' % (temp_path, value, value_crop, img_path_ffmpeg)
    cmd = shlex.split(cmd)
    print cmd
    process = Popen(cmd, stdout=PIPE)
    process.wait()
    cmd_normalize_640 = '/usr/bin/ffmpeg -i %s -vf scale=400:-1 %s -y' % (img_path_ffmpeg, temp_path)
    cmd_normalize_640 = shlex.split(cmd_normalize_640)
    print cmd_normalize_640
    process = Popen(cmd_normalize_640, stdout=PIPE)
    process.wait()
    return True

# check load url of img from database
for item in feed['entries']:
    try:
        try:
            img = pattern_jpg.search(item.summary).group().split('=')[1]
        except:
            img = pattern_png.search(item.summary).group().split('=')[1]
        img = re.sub('"', '', img)
        img = re.sub('/s146/', '/s626/', img)
        temp_path = '%s/%s' % (img_path, img.split('/')[-1])
        if os.path.exists(temp_path):
            print 'file exists == %s' % temp_path
            break
        img_path_ffmpeg = '%s/%s' % (img_path_edit, img.split('/')[-1])
        # download img from website and save as temp_path
        urllib.urlretrieve(img, temp_path)
        img_normalize(temp_path, img_path_ffmpeg)
        title = item.title
        # upload img to facebook
        photo = graph.put_photo(image=open(temp_path, 'rb'),  message=title, album_path='238338113211606/photos')
        photoid = '%s?fields=images' % photo['id']
        img_temp = graph.get_object(id=photoid)
        for i in img_temp['images']:
            source, width, height = i.items()
            photo_sql = 'insert into fb_photo_id(fb_id, height, width, link) values ("%s", %d, %d, "%s")' % (photo['id'], height[-1], width[-1], source[-1])
            cursor.execute(photo_sql)
            db.commit()
            if  height[-1] == 130:
                img_link = source[-1]
                print img_link
            if  height[-1] > 400:
                img_big = source[-1]
                print img_big
        link = item.link
        summary = item.summary.split('</a>')[-1]
        pub = item.published
        sql = "insert into alobao(link, image, title, content, pub, image_big, create_at) values ('%s', '%s', '%s', '%s', '%s', '%s', '%s')" % (link, img_link, title, summary, pub, img_big, str(datetime.now()))
        #print sql
        cursor.execute(sql)
        db.commit()
    except Exception as e:
        print link
        print str(e)
        pass
