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

sql = "select thumbnail_id, id from films where status=1"
cursor.execute(sql)
results = cursor.fetchall()

# check load url of img from database
for item in results:
    try:
        img = item[0]
        filename = img.split('/')[-1]
        temp_path = '%s/%s' % (img_path, filename)
        img_path_ffmpeg = '%s/%s' % (img_path_edit, filename)

        # download img from website and save as temp_path
        urllib.urlretrieve(img, temp_path)
        img_normalize(temp_path, img_path_ffmpeg)
        title = filename

        # upload img to facebook
        photo = graph.put_photo(image=open(temp_path, 'rb'),  message=title, album_path='238338113211606/photos')
        photoid = '%s?fields=images' % photo['id']
        img_temp = graph.get_object(id=photoid)

        # insert link of image into database
        for i in img_temp['images']:
            source, width, height = i.items()
            photo_sql = 'insert into images(fb_id, link, film_id) values ("%s", "%s", %d)' % (photo['id'], source[-1], item[-1])
            cursor.execute(photo_sql)
            db.commit()
    except Exception as e:
        print link
        pass
