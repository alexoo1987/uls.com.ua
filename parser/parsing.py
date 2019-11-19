from bs4 import BeautifulSoup
import requests
import re
import urllib2
import os
import MySQLdb


def get_soup(url,header):
    proxyUrl = ''

    proxy = urllib2.ProxyHandler( {'http': proxyUrl} )

    # Create an URL opener utilizing proxy
    opener = urllib2.build_opener( proxy )
    urllib2.install_opener( opener )


    return BeautifulSoup(urllib2.urlopen(urllib2.Request(url,headers=header)))


def getImagesFrommGoogle(string):
    image_type = "action"
    # you can change the query for the image  here
    query = string
    query= query.split()
    query='+'.join(query)
    url="https://www.google.com.ua/search?q=" + query + "&biw=1877&bih=966&source=lnms&tbm=isch&sa=X&ved=0ahUKEwiGq7z7rcTRAhXFCJoKHYsjB4sQ_AUIBigB#tbm=isch&q=" + query

    print (url)
    header = {'User-Agent': 'Mozilla/5.0'}
    soup = get_soup(url,header)

    images = [a['src'] for a in soup.find_all("img", {"src": re.compile("gstatic.com")})]

    #print first image
    if images:
        raw_img = urllib2.urlopen(images[0]).read()
        #add the directory for your image here
        DIR="/var/tecdoc_png/google/"
        cntr = len([i for i in os.listdir(DIR) if image_type in i]) + 1
        print (cntr)
        f = open(DIR + image_type + "_"+ str(cntr)+".jpg", 'wb')
        f.write(raw_img)
        f.close()
        return image_type + "_"+ str(cntr) + ".jpg"
    if not images:
        url="no-image.png"
        raw_img = url
        return raw_img


db = MySQLdb.connect("localhost", 'ulc', 'Ceifohx3zaon', 'ulc')
cursor_select = db.cursor()

query = ("SELECT id, article_long, brand_long FROM parts WHERE images IS NULL limit 10000")
cursor_select.execute(query)

for (id_s, article_long, brand_long) in cursor_select:

    query = article_long + " " + brand_long

    image_name = '/google/' + getImagesFrommGoogle(query)

    cursor_insert = db.cursor()
    insert_query = ("update parts set images = %s where id=%s ")
    cursor_insert.execute(insert_query, (image_name, id_s))
    db.commit()

    cursor_insert.close()


cursor_select.close()
db.close()



#print all geting images
"""
for img in images:
  raw_img = urllib2.urlopen(img).read()
  #add the directory for your image here
  DIR="/home/artem/python/eparts/"
  cntr = len([i for i in os.listdir(DIR) if image_type in i]) + 1
  print cntr
  f = open(DIR + image_type + "_"+ str(cntr)+".jpg", 'wb')
  f.write(raw_img)
  f.close()
"""


"""
#print first image
raw_img = urllib2.urlopen(images[0]).read()
#add the directory for your image here
DIR="/home/artem/python/eparts/"
cntr = len([i for i in os.listdir(DIR) if image_type in i]) + 1
print (cntr)
f = open(DIR + image_type + "_"+ str(cntr)+".jpg", 'wb')
f.write(raw_img)
f.close()

"""
